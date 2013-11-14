<?php
/**
 * App_Model_Seasons
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Seasons extends App_Model
{
    protected $_table_name = 'seasons';
    protected $_model_name = 'Seasons';
    protected $_primary_col = 'id';
    
    public function fetchSeasonsBySeriesId($series_id)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $sql = "SELECT s.*,
                    (SELECT COUNT(1) FROM episodes e WHERE e.series_id = s.series_id AND e.season_id = s.id) as episode_count
                FROM {$this->_table_name} s
                WHERE s.series_id = :sid
                ORDER BY s.number ASC, s.year ASC";
                
        $data = array('sid' => (int) $series_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    public function createSeason($series_id, $values)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $data = array(
            'series_id' => (int) $series_id,
            'number' => (int) $values['number'],
            'year' => (int) $values['year'],
        );
        
        return $this->insert($data);
    }
    
    /**
     * updateSeason
     * 
     * @param $id integer
     * @param $values array
     *
     * @return
     */
    public function updateSeason($id, $values)
    {
        // already exists?
        if (!$this->isInt($id)) {
            return false;
        }
        
        $data = array(
            'number' => (int) $values['number'],
            'year' => (int) $values['year'],
        );

        $where = array('id = ?' => (int) $id);

        return $this->update($data, $where);
    }
}
