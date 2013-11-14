<?php
/**
 * App_Model_EpisodesTags
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_EpisodesTags extends App_Model
{
    protected $_table_name = 'episodes_tags';
    protected $_model_name = 'EpisodesTags';
    protected $_primary_col = null;
    
    /**
     * fetchEpisodesTags
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
    public function fetchEpisodeTags($episode_id)
    {
        if (!$this->isInt($episode_id)) {
            return false;
        }
        
        $sql = "SELECT t.* FROM tags t
                INNER JOIN episodes_tags ct ON ct.tag_id = t.id
                WHERE ct.episode_id = :cid
                ORDER BY t.slug";
                
        $data = array('cid' => (int) $episode_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    /**
     * updateEpisodeTags
     * Insert description here
     *
     * @param $episode_id
     * @param $tags
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function updateEpisodeTags($episode_id, $tags) 
    {
        if (!$this->isInt($episode_id)) {
            return false;
        }
        
        $tag_ids = $this->_catalog->tags->addTags($tags);
        $this->deleteByEpisodeId($episode_id);
        
        foreach ($tag_ids as $tag_id) {
            $data = array(
                'episode_id' => (int) $episode_id,
                'tag_id' => (int) $tag_id,
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
