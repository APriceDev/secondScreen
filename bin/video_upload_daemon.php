#!/usr/bin/php
<?php
$dev = (isset($argv[2]) && $argv[2] == 'prod') ? 0 : 1;
require_once 'solarlite-bootstrap.php';

$video_id = $argv[1];
$video = $model->videos->fetchById($video_id);
if (empty($video) || !file_exists('/tmp/' . $video['file'])) {
    exit(0);
}

$filename = '/tmp/' . $video['file'];

// fingerprint video with echoprint
$parts = pathinfo($filename);
$audio_track = '/tmp/' . $parts['filename'] . '.mp3';
log_exec("/usr/bin/ffmpeg -y -i $filename -ab 160k -ac 2 -ar 44100 -vn $audio_track"); // splits video from audio
$time = exec("/usr/bin/ffmpeg -i $audio_track 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");   

$duration = explode(":",$time);   
$duration_in_seconds = ($duration[0]*3600) + ($duration[1]*60) + ceil($duration[2]);   
$start = 0;
$span = 0;
$interval = 30;
$model->videos->updateDuration($video_id, $duration_in_seconds);

$parts = pathinfo($audio_track);
$audio_track_split = '/tmp/' . $parts['filename'] . '-split.mp3';

echo PHP_EOL . 'Duration: ' . $duration_in_seconds . PHP_EOL;
while($start < $duration_in_seconds) {	
    $span = ($start + $span > $duration_in_seconds ? ($duration_in_seconds - $start) : $interval);
    //“-t” option is for the duration of your split “-ss” option is where you want your split to start (optional if you want to start at beginning of the file)
    log_exec("/usr/bin/ffmpeg -y -i $audio_track -acodec copy -t ".$span." -ss ".$start." $audio_track_split &"); 

    $cmd = '/usr/local/bin/echoprint-codegen '.$audio_track_split.' 0 30';
    $code = exec($cmd, $output); 
    $output = implode('',$output);
    $json = json_decode($output);

    // has the hash code we need. Nothing else is really relevant. 
    // This needs to be uploaded to our server, along with the reference to the split file, 
    // and other information we're interested in preserving. 
    if (isset($json[0])) {
        $obj = $json[0];
        if (!isset($obj->error)) {
            // Store in our echoprint server
            var_dump(echoprint($obj->code, $video_id, $obj->metadata->given_duration, $obj->metadata->version));
        }
    }
    $start += $interval;
}

$model->videos->updateStatus($video_id, App_Model_Videos::$statuses['s3-pending']);

// video file from tmp to S3
try {
    $bucket = SolarLite_Config::get('s3_bucket');
    $access_key = SolarLite_Config::get('aws_access_key');
    $secret_key = SolarLite_Config::get('aws_secret_key');
    $cmd = "export HOME=\"\";/usr/local/bin/s3cmd -c /home/agentile/.s3cfg put -P -M --multipart-chunk-size-mb=15 $filename s3://$bucket/{$video['file']}";
    log_exec($cmd);
    $model->videos->updateS3Info($video_id, $video['file'], $bucket);
    $model->videos->updateStatus($video_id, App_Model_Videos::$statuses['active']);
} catch (Exception $e) {
    file_put_contents('/tmp/video-process2.log', $e->getMessage(), FILE_APPEND | LOCK_EX);
}

// remove original and done!
@unlink($filename);
@unlink($audio_track);
@unlink($audio_track_split);

function echoprint($fp_code, $video_id, $length, $codever) {
    $args = array(
        'fp_code' => $fp_code,
        'track_id' => $video_id,
        'track' => $video_id,
        'length' => $length,
        'codever' => $codever
    );
    $method = 'POST';
    $headers = array("Content-Type: multipart/form-data");
    $url = BASE_URL . ':8080/ingest';
    echo $fp_code . PHP_EOL;
    
    // Add auth headers
    //$auth = base64_encode('user' . ':'. 'pass');
    //$auth_header = "Authorization: Basic $auth";
    //$headers[] = $auth_header;
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERAGENT, 'Second Screen Client');
    //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    
    if (strtolower($method) == 'post') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
    } else if (strtolower($method) == 'put') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        $url .= '?' . http_build_query($args);
    } else if (strtolower($method) == 'delete') {
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $url .= '?' . http_build_query($args);
    } else if (strtolower($method) == 'get' && !empty($args)) {
        $url .= '?' . http_build_query($args);
    }
    
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
                
    $response = curl_exec($curl);
    
    // Get the status code
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if (!$response && $http_status != 200) {
        die("Problem connecting to Second Screen Echoprint Server API.");
    }
    
    @curl_close($curl);
    
    return json_decode($response);
}

function log_exec($cmd) {
    exec($cmd, $output);
    $output = implode("\n", $output);
    file_put_contents('/tmp/video-process2.log', $output, FILE_APPEND | LOCK_EX);
}
