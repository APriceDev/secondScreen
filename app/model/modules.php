<?php
/**
 * Modules
 *
 * 
 * @package So Bella
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Modules extends App_Model
{
    protected $_table_name = 'modules';
    protected $_model_name = 'Modules';
    protected $_primary_col = 'id';
    
    
    public function fetchModules() 
    {
        $sql = "SELECT *
                FROM {$this->_table_name}
                ORDER BY id ASC";
                
        return $this->fetchAll($sql);
    }
}
