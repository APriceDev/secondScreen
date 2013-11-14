<?php
/**
 * App_Model_Genres
 * Insert description here
 *
 * @package So Bella Enterprises
 * @author Anthony Gentile <asgentile@gmail.com>
 */
class App_Model_Genres extends App_Model
{
    protected $_table_name = 'genres';
    protected $_model_name = 'Genres';
    protected $_primary_col = 'id';
    
    /**
     * addGenres
     * Insert description here
     *
     * @param $tags
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function addGenres($tags = array())
    {
        $ids = array();
        foreach ($tags as $tag) {
            $slug = App_Util::makeSlug(trim($tag));
            if (trim($slug) == '') {
                continue;
            }
            $t = $this->fetchBySlug($slug);
            if ($t) { // already exists
                $ids[] = $t['id'];
            } else {
                // do not create an empty tag
                if (trim($tag) == '') {
                    continue;
                }
                $data = array(
                    'name' => trim($tag),
                    'slug' => $slug,
                );
                $ids[] = $this->insert($data);
            }
        }
        
        return array_unique($ids);
    }

}
