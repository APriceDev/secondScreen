<?php
/**
 * App_Model_SeriesGenres
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_SeriesGenres extends App_Model
{
    protected $_table_name = 'series_genres';
    protected $_model_name = 'SeriesGenres';
    protected $_primary_col = null;
    
    /**
     * fetchSeriesGenres
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
    public function fetchSeriesGenres($series_id)
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $sql = "SELECT t.* FROM genres t
                INNER JOIN series_genres ct ON ct.genre_id = t.id
                WHERE ct.series_id = :cid
                ORDER BY t.slug";
                
        $data = array('cid' => (int) $series_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    /**
     * updateSeriesGenres
     * Insert description here
     *
     * @param $series_id
     * @param $genres
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function updateSeriesGenres($series_id, $genres) 
    {
        if (!$this->isInt($series_id)) {
            return false;
        }
        
        $tag_ids = $this->_catalog->genres->addGenres($genres);
        $this->deleteBySeriesId($series_id);
        
        foreach ($tag_ids as $tag_id) {
            $data = array(
                'series_id' => (int) $series_id,
                'genre_id' => (int) $tag_id,
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
