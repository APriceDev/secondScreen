<?php
/**
 * Videos for episodes
 *
 * 
 * @package So Bella
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Videos extends App_Model
{
    protected $_table_name = 'videos';
    protected $_model_name = 'Videos';
    protected $_primary_col = 'id';
    
    public static $statuses = array(
        'deleted' => 0,
        'active' => 1,
        'fingerprint-pending' => 2,
        's3-pending' => 3,
        'published' => 4,
    );
    
    public function fetchVideoById($id)
    {
        if (!$this->isInt($id)) {
            return false;
        }
        
        $sql = "SELECT v.*, e.description as episode_description, e.title as episode_title, srs.title as series_title, s.number
                FROM {$this->_table_name} v
                LEFT JOIN episodes e ON e.id = v.episode_id
                LEFT JOIN seasons s ON s.id = v.season_id
                LEFT JOIN series srs ON srs.id = v.series_id
                WHERE v.id = :vid";
                
        $data = array('vid' => (int) $id);
                
        return $this->fetchOne($sql, $data);
    }
    
    public function fetchVideos()
    {
        $sql = "SELECT v.*, e.title as episode_title, srs.title as series_title, s.number
                FROM {$this->_table_name} v
                LEFT JOIN episodes e ON e.id = v.episode_id
                LEFT JOIN seasons s ON s.id = v.season_id
                LEFT JOIN series srs ON srs.id = v.series_id
                ORDER BY v.date_added DESC";
                
        return $this->fetchAll($sql);
    }
    
    public function newVideo($user_id, $file, $series_id, $season_id, $episode_id, $status = 0)
    {
        $data = array(
            'user_id' => (int) $user_id,
            'file' => $file,
            'series_id' => (int) $series_id,
            'season_id' => (int) $season_id,
            'episode_id' => (int) $episode_id,
            'status' => $status, // set to 1 when move to s3 is complete
            'date_added' => App_Util::unixTimeStampUTC(),
        );

        return $this->insert($data);
    }
    
    public function processVideo($uid, $upload, $series_id, $season_id, $episode_id)
    {
        $video_id = $this->newVideo($uid, $upload['name'], $series_id, $season_id, $episode_id, self::$statuses['fingerprint-pending']);
        
        // start background process to fingerprint and move video file to Amazon S3, don't wait for it in parent script.
        $cmd = '/usr/bin/php ' . SolarLite::$system . "/bin/video_upload_daemon.php $video_id " . SS_ENV . " > /dev/null 2> /dev/null &";
        exec($cmd);

        return $upload['name'];
    }
    
    public function updateS3Info($video_id, $filename, $bucket)
    {
        $data = array('s3_bucket' => trim($bucket), 's3_filename' => trim($filename));
        $where = array('id = ?' => (int) $video_id);
        return $this->update($data, $where);
    }
    
    public function updateDuration($video_id, $seconds)
    {
        $data = array('duration' => (int) $seconds);
        $where = array('id = ?' => (int) $video_id);
        return $this->update($data, $where);
    }
    
    public function updateStatus($video_id, $status)
    {
        $data = array('status' => (int) $status);
        $where = array('id = ?' => (int) $video_id);
        return $this->update($data, $where);
    }
}
