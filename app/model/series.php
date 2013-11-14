<?php
/**
 * App_Model_Series
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Series extends App_Model
{
    protected $_table_name = 'series';
    protected $_model_name = 'Series';
    protected $_primary_col = 'id';
    
    public static $ratings = array(
        'Y',
        'Y7',
        'Y7-FV',
        'G',
        'PG',
        '14',
        'MA',
    );
    
    public function fetchSeries()
    {
        $sql = "SELECT s.*,
                    (SELECT COUNT(1) FROM seasons ss WHERE ss.series_id = s.id) as season_count
                FROM {$this->_table_name} s
                ORDER BY s.title ASC";
                
        return $this->fetchAll($sql);
    }
    
    public function createSeries($values)
    {
        if (!isset($values['title'])) {
            return false;
        }
        
        if (isset($values['release_date'])) {
            try {
                $dt = new DateTime($values['release_date'], new DateTimeZone('America/New_York'));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $rd_ts = $dt->format('U');
            } catch (Exception $e) {
                $rd_ts = App_Util::unixTimeStampUTC();
            }
        } else {
            $rd_ts = App_Util::unixTimeStampUTC();
        }
        
        if (isset($values['end_date']) && trim($values['end_date']) != '') {
            $dt = new DateTime($values['end_date'], new DateTimeZone('America/New_York'));
            $dt->setTimezone(new DateTimeZone('UTC'));
            $ed_ts = $dt->format('U');
        } else {
            $ed_ts = null;
        }
        
        $data = array(
            'title' => trim($values['title']),
            'description' => isset($values['description']) ? trim($values['description']) : null,
            'language_id' => isset($values['language_id']) ? (int) $values['language_id'] : 1,
            'rating' => (isset($values['rating']) && in_array($values['rating'], self::$ratings)) ? trim($values['rating']) : null,
            'release_date' => $rd_ts,
            'end_date' => $ed_ts,
        );
        
        return $this->insert($data);
    }
    
    /**
     * updateSeries
     * 
     * @param $id integer
     * @param $values array
     *
     * @return
     */
    public function updateSeries($id, $values)
    {
        // already exists?
        if (!$this->isInt($id)) {
            return false;
        }
        
        if (!isset($values['title'])) {
            return false;
        }
        
        if (isset($values['release_date'])) {
            try {
                $dt = new DateTime($values['release_date'], new DateTimeZone('America/New_York'));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $rd_ts = $dt->format('U');
            } catch (Exception $e) {
                $rd_ts = App_Util::unixTimeStampUTC();
            }
        } else {
            $rd_ts = App_Util::unixTimeStampUTC();
        }
        
        if (isset($values['end_date']) && trim($values['end_date']) != '') {
            $dt = new DateTime($values['end_date'], new DateTimeZone('America/New_York'));
            $dt->setTimezone(new DateTimeZone('UTC'));
            $ed_ts = $dt->format('U');
        } else {
            $ed_ts = null;
        }

        $data = array(
            'title' => trim($values['title']),
            'description' => isset($values['description']) ? trim($values['description']) : null,
            'language_id' => isset($values['language_id']) ? (int) $values['language_id'] : 1,
            'rating' => (isset($values['rating']) && in_array($values['rating'], self::$ratings)) ? trim($values['rating']) : null,
            'release_date' => $rd_ts,
            'end_date' => $ed_ts,
        );

        $where = array('id = ?' => (int) $id);

        return $this->update($data, $where);
    }
}
