<?php
/**
 * App_Util
 *
 * @category Second Screen
 * @package  Sobella Enterprises
 * @author   Anthony Gentile <agentilex@sobellaenterprises.com>
 * @license  Proprietary/Closed Source
 */ 
class App_Util
{
    /**
     * Get UNIX timestamp in UTC.
     * 
     * @return void
     * @access public
     * @static
     */
    public static function unixTimeStampUTC()
    {
        // all unix timestamp fields in the database should be UTC.
        $dt = new DateTime('@' . time(), new DateTimeZone('America/New_York'));
        $dt->setTimezone(new DateTimeZone('UTC'));
        $ts = $dt->format('U');
        return $ts;
    }
    
    public static function isInt($value) 
    {
        if (is_int($value)) {
            return true;
        }
        
        // otherwise, must be numeric, and must be same as when cast to int
        return is_numeric($value) && $value == (int) $value;
    }
    
    /**
     * formatDate function.
     * 
     * @param mixed  $value    UNIX timestamp or date string
     * @param int    $offset   (default: 0) Offset for value
     * @param string $timezone (default: 'America/New_York') timezone to switch to for user
     * @param string $format   (default: 'F jS, Y') Format
     *
     * @return void
     * @access public
     * @static
     */
    public static function formatDate($value, $offset = 0, $timezone = 'America/New_York', $format = 'F jS, Y')
    {
        if (!$timezone) {
            $timezone = 'America/New_York';
        }
        if (is_numeric($value)) {
            $dt = new DateTime("@{$value}", new DateTimeZone(self::timeZoneFromOffset($offset)));
            $dt->setTimezone(new DateTimeZone($timezone));
            return $dt->format($format);
        } else {
            $dt = new DateTime($value, new DateTimeZone(self::timeZoneFromOffset($offset)));
            $dt->setTimezone(new DateTimeZone($timezone));
            return $dt->format($format);
        }
    }
    
    /**
     * Create an array of tags to be inserted
     * into the db from a given string. 
     * 
     * @param mixed $tags_string Tags string
     *
     * @return void
     * @access public
     * @static
     */
    public static function tagsArray($tags_string)
    {
        $tags_raw = explode(',', rtrim(trim($tags_string), ','));
        
        $tags = array();
        foreach($tags_raw as $t) {
            if (substr($t, -1) == '.') {
                $t = substr($t, 0, -1);
            }
            $tags[] = $t;
        }
        
        return array_values(array_unique($tags));
    }

    /**
     * timeZoneFromOffset function.
     * 
     * @param mixed $offset Offset
     *
     * @return void
     * @access public
     * @static
     */
    public static function timeZoneFromOffset($offset)
    {
        $offset = (string) $offset;
        $timezones = array(
            '-12'  => 'Pacific/Kwajalein',
            '-11'  => 'Pacific/Samoa',
            '-10'  => 'Pacific/Honolulu',
            '-9'   => 'America/Juneau',
            '-8'   => 'America/Los_Angeles',
            '-7'   => 'America/Denver',
            '-6'   => 'America/Mexico_City',
            '-5'   => 'America/New_York',
            '-4'   => 'America/Caracas',
            '-3.5' => 'America/St_Johns',
            '-3'   => 'America/Argentina/Buenos_Aires',
            '-2'   => 'Atlantic/Azores',// No cities here so just picking an hour ahead
            '-1'   => 'Atlantic/Azores',
            '0'    => 'Europe/London', // UTC
            '1'    => 'Europe/Paris',
            '2'    => 'Europe/Helsinki',
            '3'    => 'Europe/Moscow',
            '3.5'  => 'Asia/Tehran',
            '4'    => 'Asia/Baku',
            '4.5'  => 'Asia/Kabul',
            '5'    => 'Asia/Karachi',
            '5.5'  => 'Asia/Calcutta',
            '6'    => 'Asia/Colombo',
            '7'    => 'Asia/Bangkok',
            '8'    => 'Asia/Singapore',
            '9'    => 'Asia/Tokyo',
            '9.5'  => 'Australia/Darwin',
            '10'   => 'Pacific/Guam',
            '11'   => 'Asia/Magadan',
            '12'   => 'Asia/Kamchatka'
        );
        return $timezones[$offset];
    }

    /**
     * getIP function.
     * 
     * @return void
     * @access public
     * @static
     */
    public static function getIP()
    {
        $ip = '0.0.0.0';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_VIA'])) {
            $ip = $_SERVER['HTTP_VIA'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * relativeTime function.
     * 
     * @param mixed $time Time
     *
     * @return void
     * @access public
     * @static
     */
    public static function relativeTime($time)
    {
        $divisions = array(1,60,60,24,7,4.34,12);
        $names = array('second','minute', 'hour', 'day', 'week', 'month', 'year');
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        if ($time === false) {
            return ""; // Unknown
        }

        $time = time() - $time;
        $name = "";

        if ($time < 10) {
            return "just now";
        }

        for ($i=0; $i<count($divisions); $i++) {
            if ($time < $divisions[$i]) break;

            $time = $time/$divisions[$i];
            $name = $names[$i];
        }

        $time = round($time);

        if ($time != 1) {
            $name .= 's';
        }

        return "$time $name ago";
    }

    /**
     * Changes a string to url friendly slug
     *
     * @param string $str        the input  string
     * @param string $delim      [optional] the word delimiter; defaults to '-'
     * @param int    $max_length [optional] the max slug length; defaults to 75
     *
     * @return string the slug
     */
    public static function makeSlug($str, $delim = '-', $max_length = 75)
    {
        $str = str_replace("'", '', $str); // we don't want apostrophes hyphenated
        $str = preg_replace('/[^a-z0-9-]/', $delim, strtolower(trim($str)));
        $str = preg_replace("/{$delim}+/", $delim, trim($str, $delim));
        return rtrim(substr($str, 0, $max_length), $delim);
    }

    /**
     * makePassphrase function.
     * 
     * @param int $length (default: 10)
     *
     * @return void
     * @access public
     * @static
     */
    public static function makePassphrase($length = 10)
    {
        $V  = array("a", "e", "i", "o", "u", "y");
        $VN = array("a", "e", "i", "o", "u", "y","2","3","4","5","6","7","8","9");
        $C  = array("b","c","d","f","g","h","j","k","m","n","p","q","r","s","t","u","v","w","x","z");
        $CN = array("b","c","d","f","g","h","j","k","m","n","p","q","r","s","t","u","v","w","x","z","2","3","4","5","6","7","8","9");
        $word       = '';
        $wordLen    = $length;
        $vowels     = $VN;
        $consonants = $CN;

        for ($i=0; $i < $wordLen; $i = $i + 2) {
            // generate word with mix of vowels and consonants
            $consonant = $consonants[array_rand($consonants)];
            $vowel     = $vowels[array_rand($vowels)];
            $word     .= $consonant . $vowel;
        }

        if (strlen($word) > $wordLen) {
            $word = substr($word, 0, $wordLen);
        }

        return $word;
    }
    
    /**
     * remoteFileExists function.
     * 
     * @param mixed $url URL
     *
     * @return void
     * @access public
     * @static
     */
    public static function remoteFileExists($url)
    {
        $curl = curl_init($url);

        // don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        // do request
        $result = curl_exec($curl);

        $ret = false;

        // if request did not fail
        if ($result !== false) {
            // if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $ret = true;
            }
        }

        curl_close($curl);

        return $ret;
    }

    /**
     * Given a file, i.e. /css/base.css, replaces it with a string containing the file's mtime,
     * i.e. /css/base.1221534296.css.
     * 
     * @param mixed $file File
     *
     * @return void
     * @access public
     * @static
     */
    public static function auto_version($file) {
        if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
            return $file;
        }
        $mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
        return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
    }
}
