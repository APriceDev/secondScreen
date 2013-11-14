<?php
/**
 * App_Model_Actors
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Actors extends App_Model
{
    protected $_table_name = 'actors';
    protected $_model_name = 'Actors';
    protected $_primary_col = 'id';
    
    public function fetchByName($name)
    {
        $sql = "SELECT id FROM {$this->_table_name} WHERE name LIKE :name";
        $data = array('name' => strtolower($name));
        $ret = $this->fetchOne($sql, $data);
        
        return $ret ? $ret['id'] : null;
    }
    
    public function createActorBlank($name)
    {
        $data = array(
            'name' => trim($name),
        );
        
        return $this->insert($data);
    }
}
