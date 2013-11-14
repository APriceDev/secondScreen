<?php
/**
 * Video Events Metadata
 *
 * 
 * @package So Bella
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_EventOptions extends App_Model
{
    protected $_table_name = 'event_options';
    protected $_model_name = 'EventOptions';
    protected $_primary_col = 'id';
    
    
    public function fetchByEventId($event_id)
    {
        if (!$this->isInt($event_id)) {
            return false;
        }
        
        $sql = "SELECT id, name, type, value
                FROM {$this->_table_name} 
                WHERE event_id = :id";
                
        $data = array('id' => $event_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    public function deleteByEventId($event_id)
    {
        if (!$this->isInt($event_id)) {
            return false;
        }
        
        $where = array('event_id = ?' => array($event_id));
        return $this->delete($where);
    }
    
    public static function getByKey($data, $key, $ret = null) 
    {
        foreach ($data as $k => $v) {
            if ($v['name'] == $key) {
                return $v['value'];
            }
        }
        return $ret;
    }
    
    public function updateEventOptions($event_id, $data)
    {
        if (!$this->isInt($event_id)) {
            return false;
        }
        
        foreach ($data as $k => $v) {
            $this->addEventOptionValue($event_id, $k, $v);
        }
    }
    
    public function fetchByEventAndName($event_id, $name)
    {
        $sql = "SELECT id, name, type, value
                FROM {$this->_table_name} 
                WHERE event_id = :id AND name = :n";
        $data = array('id' => $event_id, 'n' => trim($name));

        return $this->fetchOne($sql, $data);
    }
    
    public function addEventOptionValue($event_id, $name, $value)
    {
        $exists = $this->fetchByEventAndName($event_id, $name);
        
        if ($exists) {
            $data = array('value' => $value);
            $where = array('id = ?' => array($exists['id']));
            $this->update($data, $where);
        } else {
            $data = array(
                'event_id' => (int) $event_id,
                'name' => $name,
                'value' => $value
            );
            
            $this->insert($data);
        }
    }
}
