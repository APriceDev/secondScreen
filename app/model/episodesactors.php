<?php
/**
 * App_Model_EpisodesActors
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_EpisodesActors extends App_Model
{
    protected $_table_name = 'episodes_actors';
    protected $_model_name = 'EpisodesActors';
    protected $_primary_col = null;
    
    /**
     * fetchEpisodeActors
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
    public function fetchEpisodeActors($episode_id)
    {
        if (!$this->isInt($episode_id)) {
            return false;
        }
        
        $sql = "SELECT t.* FROM actors t
                INNER JOIN episodes_actors ct ON ct.actor_id = t.id
                WHERE ct.episode_id = :cid";
                
        $data = array('cid' => (int) $episode_id);
        
        return $this->fetchAll($sql, $data);
    }
    
    public function fetchRelationship($episode_id, $actor_id)
    {
        if (!$this->isInt($episode_id) || !$this->isInt($actor_id)) {
            return false;
        }
        
        $sql = "SELECT * FROM {$this->_table_name}
                WHERE episode_id = :cid AND actor_id = :aid";
                
        $data = array('cid' => (int) $episode_id, 'aid' => (int) $actor_id);
        
        return $this->fetchOne($sql, $data);
    }
    
    public function createRelationship($episode_id, $actor_id, $character_name)
    {
        if (!$this->isInt($episode_id) || !$this->isInt($actor_id)) {
            return false;
        }
        
        if ($this->fetchRelationship($episode_id, $actor_id)) {
            return false;
        }
        
        $data = array(
            'episode_id' => (int) $episode_id,
            'actor_id' => (int) $actor_id,
            'character_name' => $character_name,
        );
        
        $this->insert($data);
    }
}
