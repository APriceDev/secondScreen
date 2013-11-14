<?php
/**
 * App_Model_SeriesTags
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_SeriesTags extends App_Model
{
    protected $_table_name = 'series_tags';
    protected $_model_name = 'SeriesTags';
    protected $_primary_col = null;
    
    /**
     * fetchSeriesTags
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
    public function fetchSeriesTags($series_id)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $sql = "SELECT t.* FROM tags t
                INNER JOIN series_tags ct ON ct.tag_id = t.id
                WHERE ct.series_id = :cid
                ORDER BY t.slug";
                
        $data = array('cid' => (int) $series_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    /**
     * updateSeriesTags
     * Insert description here
     *
     * @param $series_id
     * @param $tags
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function updateSeriesTags($series_id, $tags) 
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $tag_ids = $this->_catalog->tags->addTags($tags);
        $this->deleteBySeriesId($series_id);
        
        foreach ($tag_ids as $tag_id) {
            $data = array(
                'series_id' => (int) $series_id,
                'tag_id' => (int) $tag_id,
            );
            
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
