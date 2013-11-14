<?php
/**
 * App_Model_EpisodesGenres
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_EpisodesGenres extends App_Model
{
    protected $_table_name = 'episodes_genres';
    protected $_model_name = 'EpisodesGenres';
    protected $_primary_col = null;
    
    /**
     * fetchEpisodesGenres
     * Insert description here
     *
     * @param $episode_id
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function fetchEpisodeGenres($episode_id)
    {
        if (!$this->isInt($episode_id)) {
            return false;
        }
        
        $sql = "SELECT t.* FROM genres t
                INNER JOIN episodes_genres ct ON ct.genre_id = t.id
                WHERE ct.episode_id = :cid
                ORDER BY t.slug";
                
        $data = array('cid' => (int) $episode_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    /**
     * updateEpisodeGenres
     * Insert description here
     *
     * @param $episode_id
     * @param $genres
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function updateEpisodeGenres($episode_id, $genres) 
    {
        if (!$this->isInt($episode_id)) {
            return false;
        }
        
        $tag_ids = $this->_catalog->genres->addGenres($genres);
        $this->deleteByEpisodeId($episode_id);
        
        foreach ($tag_ids as $tag_id) {
            $data = array(
                'episode_id' => (int) $episode_id,
                'genre_id' => (int) $tag_id,
            );
            
            $this->insert($data);
        }
    }
    
    /**
     * deleteByEpisodesId
     * Insert description here
     *
     * @param $episode_id
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function deleteByEpisodeId($episode_id)
    {
        if (!$this->isInt($episode_id)) {
            return false;
        }
        
        $where = array('episode_id = ?' => (int) $episode_id);
        $this->delete($where);
    }
}
