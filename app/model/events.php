<?php
/**
 * Video Events
 *
 * 
 * @package So Bella
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Events extends App_Model
{
    protected $_table_name = 'events';
    protected $_model_name = 'Events';
    protected $_primary_col = 'id';
    
    public static $statuses = array(
        'inactive' => 0,
        'active' => 1,
    );
    
    public function fetchEventById($event_id)
    {
        if (!$this->isInt($event_id)) {
            return false;
        }
        
        $sql = "SELECT e.*, m.name as module_name, m.slug
                FROM {$this->_table_name} e
                INNER JOIN modules m ON m.id = e.module_id
                WHERE e.id = :id";
                
        $data = array('id' => $event_id);
        
        return $this->fetchOne($sql, $data);
    }
    
    public function fetchByVideoIdExpanded($video_id) 
    {
        if (!$this->isInt($video_id)) {
            return false;
        }
        
        $sql = "SELECT e.*, m.name as module_name, m.color
                FROM {$this->_table_name} e
                INNER JOIN modules m ON m.id = e.module_id
                WHERE e.video_id = :vid
                ORDER BY e.start_second ASC, e.end_second DESC";
                
        $data = array('vid' => $video_id);
        
        $results = $this->fetchAll($sql, $data);
        
        foreach ($results as $k => $v) {
            $results[$k]['event_options'] = $this->_catalog->event_options->fetchByEventId($v['id']);
        }
        
        return $results;
    }
    
    public function fetchByVideoId($video_id)
    {
        if (!$this->isInt($video_id)) {
            return false;
        }
        
        $sql = "SELECT e.*, m.name as module_name, m.color
                FROM {$this->_table_name} e
                INNER JOIN modules m ON m.id = e.module_id
                WHERE e.video_id = :vid
                ORDER BY e.start_second ASC, e.end_second DESC";
                
        $data = array('vid' => $video_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    public function updateVideoEvent($event_id, $start_sec, $end_sec)
    {
        $data = array(
            'start_second' => (int) $start_sec,
            'end_second' => (int) $end_sec,
            'duration' => (int) $end_sec - (int) $start_sec,
        );
        $where = array('id = ?' => (int) $event_id);
        return $this->update($data, $where);
    }
    
    public function newVideoEvent($video_id, $module_id, $start_sec, $end_sec)
    {
        if (!$this->isInt($video_id) || !$this->isInt($module_id) || !$this->isInt($start_sec) || !$this->isInt($end_sec)) {
            return false;
        }
        
        $data = array(
            'video_id' => (int) $video_id,
            'module_id' => (int) $module_id,
            'start_second' => (int) $start_sec,
            'end_second' => (int) $end_sec,
            'duration' => $end_sec - $start_sec,
        );

        return $this->insert($data);
    }
}
