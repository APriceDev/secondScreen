<?php
/**
 * App_Model_Episodes
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Episodes extends App_Model
{
    protected $_table_name = 'episodes';
    protected $_model_name = 'Episodes';
    protected $_primary_col = 'id';
    
    public function fetchEpisodesBySeriesIdAndSeasonId($series_id, $season_id)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $sql = "SELECT e.*,
                    (SELECT COUNT(1) FROM episodes_actors WHERE episode_id = e.id) as actor_count
                FROM {$this->_table_name} e
                WHERE e.series_id = :sid AND e.season_id = :ssid
                ORDER BY e.number ASC, e.release_date ASC";
                
        $data = array('sid' => (int) $series_id, 'ssid' => (int) $season_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    public function fetchEpisodesBySeasonId($season_id)
    {
        if (!$this->isInt($season_id)) {
            return false;
        }
        
        $sql = "SELECT e.*,
                    (SELECT COUNT(1) FROM episodes_actors WHERE episode_id = e.id) as actor_count
                FROM {$this->_table_name} e
                WHERE e.season_id = :ssid
                ORDER BY e.number ASC, e.release_date ASC";
                
        $data = array('ssid' => (int) $season_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    public function createEpisode($series_id, $season_id, $values)
    {
        if (!$this->isInt($series_id) || !$this->isInt($season_id)) {
            return false;
        }
        
        if (isset($values['release_date']) && trim($values['release_date']) != '') {
            try {
                $dt = new DateTime($values['release_date'], new DateTimeZone('America/New_York'));
                $dt->setTimezone(new DateTimeZone('UTC'));
                $rd_ts = $dt->format('U');
            } catch (Exception $e) {
                $rd_ts = null;
            }
        } else {
            $rd_ts = null;
        }
        
        $data = array(
            'series_id' => (int) $series_id,
            'season_id' => (int) $season_id,
            'number' => (int) $values['number'],
            'title' => trim($values['title']),
            'description' => isset($values['description']) ? trim($values['description']) : null,
            'rating' => (isset($values['rating']) && in_array($values['rating'], App_Model_Series::$ratings)) ? trim($values['rating']) : null,
            'release_date' => $rd_ts,
        );
        
        return $this->insert($data);
    }
    
    /**
     * updateEpisode
     * 
     * @param $id integer
     * @param $values array
     *
     * @return
     */
    public function updateEpisode($id, $values)
    {
        if (!$this->isInt($id)) {
            return false;
        }
        
        if (isset($values['release_date']) && trim($values['release_date']) != '') {
            $dt = new DateTime($values['release_date'], new DateTimeZone('America/New_York'));
            $dt->setTimezone(new DateTimeZone('UTC'));
            $rd_ts = $dt->format('U');
        } else {
            $rd_ts = null;
        }
        
        $data = array(
            'number' => (int) $values['number'],
            'title' => trim($values['title']),
            'description' => isset($values['description']) ? trim($values['description']) : null,
            'rating' => (isset($values['rating']) && in_array($values['rating'], App_Model_Series::$ratings)) ? trim($values['rating']) : null,
            'release_date' => $rd_ts,
        );

        $where = array('id = ?' => (int) $id);

        return $this->update($data, $where);
    }
    
    public function updateDescription($id, $desc)
    {
        if (!$this->isInt($id)) {
            return false;
        }
        
        $data = array(
            'description' => trim($desc),
        );

        $where = array('id = ?' => (int) $id);

        $this->update($data, $where);
        $this->_catalog->videos->clearCache();
    }
}
