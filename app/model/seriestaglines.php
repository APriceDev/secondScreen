<?php
/**
 * App_Model_SeriesTaglines
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_SeriesTaglines extends App_Model
{
    protected $_table_name = 'series_taglines';
    protected $_model_name = 'SeriesTaglines';
    protected $_primary_col = null;
    
    public function fetchBySeriesId($series_id)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $sql = "SELECT tagline FROM {$this->_table_name} WHERE series_id = :s";
        $data = array('s' => (int) $series_id);
        
        $res = $this->fetchAll($sql, $data);
        $arr = array();
        foreach ($res as $r) {
            $arr[] = $r['tagline'];
        }
        
        return $arr;
    }
    
    public function updateTaglines($series_id, $taglines)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $this->deleteBySeriesId($series_id);
        foreach ($taglines as $tl) {
            $data = array('series_id' => $series_id, 'tagline' => trim($tl));
            $this->insert($data);
        }
    }
    
    /**
     * deleteBySeriesId
     * Insert description here
     *
     * @param $series_id
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function deleteBySeriesId($series_id)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $where = array('series_id = ?' => (int) $series_id);
        $this->delete($where);
    }
}
