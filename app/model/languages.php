<?php
/**
 * App_Model_Languages
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Languages extends App_Model
{
    protected $_table_name = 'languages';
    protected $_model_name = 'Languages';
    protected $_primary_col = 'id';
    
    public function fetchLanguages()
    {
        $sql = "SELECT id, name
                FROM {$this->_table_name} 
                ORDER BY id ASC";
                
        return $this->fetchAll($sql);
    }
    
    public function fetchLanguageByName($name)
    {
        $sql = "SELECT id FROM {$this->_table_name} WHERE name LIKE :name";
        $data = array('name' => ucfirst(strtolower($name)));
        $ret = $this->fetchOne($sql, $data);
        
        return $ret ? $ret['id'] : null;
    }
}
