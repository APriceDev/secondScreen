<?php
class App_Controller_Admin extends App_Controller_Base
{
    protected $_action_default = 'index';
    
    public $permissions = array();
    public $permission = array();
    
    public $group_permissions = array();
    
    public $groups = array();
    public $group = array();
    
    public $users = array();
    public $user = array();
    
    public $series = array();
    public $seasons = array();
    public $season = array();
    public $episodes = array();
    public $episode = array();
    
    public $videos = array();
    public $video = array();
    public $modules = array();
    
    public $languages = array();

    public function actionIndex()
    {
    }
    
    public function actionVideos()
    {
        $this->_checkPermission('admin_videos_view');
        $this->page_title .= ' - Manage Videos';
        
        if ($this->_request->post('process')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            foreach ($this->_request->post('selected') as $id) {
                if ($this->_request->post('action') == 'delete' && $this->_hasPermission('admin_videos_delete')) {
                    $this->_model->videos->deleteById($id);
                }
            }
        }
        
        $this->videos = $this->_model->videos->fetchVideos();
    }
    
    public function actionTimeline($video_id = null)
    {
        $this->_checkPermission('admin_videos_edit');
        
        $this->video = $this->_model->videos->fetchVideoById($video_id);
        
        if (!$this->video) {
            $this->_setError('Invalid Video ID', 'No video id was given.');
            $this->_redirect('/admin/videos');
        }
        
        $duration = is_numeric($this->video['duration']) ? $this->video['duration'] : 0;
        $this->modules = $this->_model->modules->fetchModules();
        $this->js_args = "$duration, $video_id";
    }
    
    public function actionQuickAdd()
    {
        $this->page_title .= ' - Quick Add';
    }
    
    public function actionUpload()
    {
        $this->_checkPermission('admin_videos_create');
        $this->swfobject = true;
        $this->series = $this->_model->series->fetchSeries();
        
        if ($this->_request->post('submit')) {
            if (!$this->_request->post('series_id') || !$this->_request->post('season_id') || !$this->_request->post('episode_id')) {
                $this->_setError('Fields Missing', 'Please select a Series, Season and Episode for this video.');
                return;
            }
            
            if (!$this->_request->post('video_file')) {
                $this->_setError('Fields Missing', 'Missing uploaded video.');
                return;
            }
            
            $upload = array(
                'type' => $this->_request->post('video_type'),
                'name' => $this->_request->post('video_file')
            );
            
            if (!$this->_isValidVideo($upload)) {
                return;
            }
            
            $this->_model->videos->processVideo(
                $this->logged_in_id,
                $upload,
                $this->_request->post('series_id'),
                $this->_request->post('season_id'),
                $this->_request->post('episode_id')
            );
            
            $this->_setMessage('Video Uploaded', "Video successfully uploaded!");
            $this->_redirect('/admin/videos');
        }
    }
    
    public function actionEditGroup($id = null)
    {
        $this->_checkPermission('admin_groups_edit');
        
        if (is_null($id)) {
            $this->_setError('Invalid Group ID', 'No group id was given.');
            $this->_redirect('/admin/groups');
        }
        
        $this->group = $this->_model->groups->fetchById($id);
        if (!$this->group) {
            $this->_setError('Invalid Group ID', 'That is not a valid group id.');
            $this->_redirect('/admin/groups');
        }
        
        $this->page_title .= ' - Edit Group';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            if (!$this->_request->post('name') || trim($this->_request->post('name')) == '') {
                $this->_setError('Name Left Blank', 'Group name cannot be blank.');
                return;
            }
            
            if (!$this->_request->post('description') || trim($this->_request->post('description')) == '') {
                $this->_setError('Description Left Blank', 'Group description cannot be blank.');
                return;
            }

            // no errors, woot
            $this->_model->groups->updateGroup($this->_request->post('id'), $this->_request->post('name'), $this->_request->post('description'));
            $this->_model->groups_permissions->deleteByGroup($this->_request->post('id'));
            foreach ($this->_request->post('permissions', array()) as $pid) {
                $this->_model->groups_permissions->addGroupPermission($this->_request->post('id'), $pid);
            }
            
            $this->_setMessage('Group Updated', 'Group successfully updated.');
            $this->_redirect('/admin/groups');
        }
        
        $this->permissions = $this->_model->permissions->fetchPermissions();
        $this->group_permissions = $this->_model->groups_permissions->fetchPermissionsIdListByGroup($id);
    }
    
    public function actionNewGroup()
    {
        $this->_checkPermission('admin_groups_create');
        $this->page_title .= ' - Create Group';
        
        $this->form_values = $this->_request->post;
        
        $this->permissions = $this->_model->permissions->fetchPermissions();
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            if (!$this->_request->post('name') || trim($this->_request->post('name')) == '') {
                $this->_setError('Name Left Blank', 'Permission name cannot be blank.');
                return;
            }
            
            if (!$this->_request->post('description') || trim($this->_request->post('description')) == '') {
                $this->_setError('Description Left Blank', 'Permission description cannot be blank.');
                return;
            }

            // no errors, woot
            $group_id = $this->_model->groups->createGroup($this->_request->post('name'), $this->_request->post('description'));
            foreach ($this->_request->post('permissions', array()) as $pid) {
                $this->_model->groups_permissions->addGroupPermission($group_id, $pid);
            }
            $this->_setMessage('Group Created', 'Group successfully created.');
            $this->_redirect('/admin/groups');
        }
    }
    
    public function actionGroups()
    {
        $this->_checkPermission('admin_groups_view');
        
        $this->page_title .= ' - Manage Groups';
        
        if ($this->_request->post('process')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            foreach ($this->_request->post('selected') as $id) {
                if ($this->_request->post('action') == 'delete' && $this->_hasPermission('admin_groups_delete')) {
                    $this->_model->groups->deleteById($id);
                }
            }
        }
        
        $this->groups = $this->_model->groups->fetchGroups();
    }
    
    public function actionPermissions()
    {
        $this->_checkPermission('admin_permissions_view');
        $this->page_title .= ' - Manage Permissons';
        
        if ($this->_request->post('process')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            foreach ($this->_request->post('selected') as $id) {
                if ($this->_request->post('action') == 'delete') {
                    // attempted to edit a default permission? disallow.
                    $permission = $this->_model->permissions->fetchById($id);
                    if (in_array($permission['name'], array_keys($this->_model->permissions->default_permissions))) {
                        $this->_setError('Restricted Action', 'You may not delete default permission: ' . $permission['name']);
                    } else {
                        if ($this->_hasPermission('admin_permissions_delete')) {
                            $this->_model->permissions->deleteById($id);
                        }
                    }
                }
            }
        }
        
        $this->permissions = $this->_model->permissions->fetchPermissions();
    }
    
    public function actionNewPermission()
    {
        $this->_checkPermission('admin_permissions_create');
        $this->page_title .= ' - Create Permission';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            if (!$this->_request->post('name') || trim($this->_request->post('name')) == '') {
                $this->_setError('Name Left Blank', 'Permission name cannot be blank.');
                return;
            }
            
            if (!$this->_request->post('description') || trim($this->_request->post('description')) == '') {
                $this->_setError('Description Left Blank', 'Permission description cannot be blank.');
                return;
            }

            // no errors, woot
            $this->_model->permissions->createPermission($this->_request->post('name'), $this->_request->post('description'));
            $this->_setMessage('Permission Created', 'Permission successfully created.');
            $this->_redirect('/admin/permissions');
        }
    }
    
    public function actionEditPermission($id = null)
    {
        $this->_checkPermission('admin_permissions_edit');
        if (is_null($id)) {
            $this->_setError('Invalid Permission ID', 'No permission id was given.');
            $this->_redirect('/admin/permissions');
        }
        
        $this->permission = $this->_model->permissions->fetchById($id);
        if (!$this->permission) {
            $this->_setError('Invalid Permission ID', 'That is not a valid permission id.');
            $this->_redirect('/admin/permissions');
        }
        
        $this->page_title .= ' - Edit Permission';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            if (!$this->_request->post('name') || trim($this->_request->post('name')) == '') {
                $this->_setError('Name Left Blank', 'Permission name cannot be blank.');
                return;
            }
            
            if (!$this->_request->post('description') || trim($this->_request->post('description')) == '') {
                $this->_setError('Description Left Blank', 'Permission description cannot be blank.');
                return;
            }

            // no errors, woot
            $this->_model->permissions->updatePermission($this->_request->post('id'), $this->_request->post('name'), $this->_request->post('description'));
            $this->_setMessage('Permission Updated', 'Permission successfully updated.');
            $this->_redirect('/admin/permissions');
        }
    }
    
    public function actionSeries()
    {
        $this->_checkPermission('admin_series_view');
        $this->page_title .= ' - Manage Series';
        
        if ($this->_request->post('process')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            foreach ($this->_request->post('selected') as $id) {
                if ($this->_request->post('action') == 'delete' && $this->_hasPermission('admin_series_delete')) {
                    $this->_setError('Unallowed action', 'This action can have far reaching effects on child entities (seasons, episodes, etc) please talk to an administrator to delete this.');
                    //$this->_model->users->deleteById($id);
                }
            }
        }
        
        $this->series = $this->_model->series->fetchSeries();
    }
    
    public function actionEditSeason($id = null, $season_id = null)
    {
        $this->_checkPermission('admin_series_edit');
        if (is_null($id)) {
            $this->_setError('Invalid Series ID', 'No series id was given.');
            $this->_redirect('/admin/series');
        }
        
        $this->series = $this->_model->series->fetchById($id);
        if (!$this->series) {
            $this->_setError('Invalid Series ID', 'That is not a valid series id.');
            $this->_redirect('/admin/series');
        }
        
        if (is_null($season_id)) {
            $this->_setError('Invalid Season ID', 'No season id was given.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        $this->season = $this->_model->seasons->fetchById($season_id);
        if (!$this->season) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        if ($this->season['series_id'] != $this->series['id']) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        $this->page_title .= ' - Edit Season';
        $this->form_values = $this->_request->post;
        
        
        if ($this->_request->post('submit')) {
            $this->series = array_merge($this->series, $this->form_values);
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            // no errors, woot
            $this->_model->seasons->updateSeason($this->season['id'], $this->form_values);
            
            $this->_setMessage('Season Updated', 'Season successfully updated.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
    }
    
    public function actionNewSeason($id = null)
    {
        $this->_checkPermission('admin_season_create');
        if (is_null($id)) {
            $this->_setError('Invalid Series ID', 'No series id was given.');
            $this->_redirect('/admin/series');
        }
        
        $this->series = $this->_model->series->fetchById($id);
        if (!$this->series) {
            $this->_setError('Invalid Series ID', 'That is not a valid series id.');
            $this->_redirect('/admin/series');
        }
        
        $this->page_title .= ' - Create Season';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            // no errors, woot
            $id = $this->_model->seasons->createSeason($this->series['id'], $this->form_values);
            
            $this->_setMessage('Season Created', 'Season successfully created.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
    }
    
    public function actionSeasons($id = null)
    {
        $this->_checkPermission('admin_seasons_view');
        $this->page_title .= ' - Manage Seasons';
        
        if (is_null($id)) {
            $this->_setError('Invalid Series ID', 'No series id was given.');
            $this->_redirect('/admin/series');
        }
        
        $this->series = $this->_model->series->fetchById($id);
        if (!$this->series) {
            $this->_setError('Invalid Series ID', 'That is not a valid series id.');
            $this->_redirect('/admin/series');
        }
        
        if ($this->_request->post('process')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            foreach ($this->_request->post('selected') as $id) {
                if ($this->_request->post('action') == 'delete' && $this->_hasPermission('admin_seasons_delete')) {
                    $this->_setError('Unallowed action', 'This action can have far reaching effects on child entities (episodes, etc) please talk to an administrator to delete this.');
                    //$this->_model->users->deleteById($id);
                }
            }
        }
        
        $this->seasons = $this->_model->seasons->fetchSeasonsBySeriesId($this->series['id']);
    }
    
    public function actionEditEpisode($id = null, $season_id = null, $episode_id = null)
    {
        $this->_checkPermission('admin_series_edit');
        if (is_null($id)) {
            $this->_setError('Invalid Series ID', 'No series id was given.');
            $this->_redirect('/admin/series');
        }
        
        $this->series = $this->_model->series->fetchById($id);
        if (!$this->series) {
            $this->_setError('Invalid Series ID', 'That is not a valid series id.');
            $this->_redirect('/admin/series');
        }
        
        if (is_null($season_id)) {
            $this->_setError('Invalid Season ID', 'No season id was given.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        $this->season = $this->_model->seasons->fetchById($season_id);
        if (!$this->season) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        if ($this->season['series_id'] != $this->series['id']) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        $this->episode = $this->_model->episodes->fetchById($episode_id);
        if (!$this->episode) {
            $this->_setError('Invalid Episode ID', 'That is not a valid episode id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons/' . $this->season['id'] . '/episodes');
        }
        
        if ($this->season['series_id'] != $this->episode['series_id'] || $this->season['id'] != $this->episode['season_id']) {
            $this->_setError('Invalid Episode ID', 'That is not a valid episode id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons/' . $this->season['id'] . '/episodes');
        }
        
        $this->page_title .= ' - Edit Episode';
        $this->form_values = $this->_request->post;
        
        // Tags
        $tags = $this->_model->episodes_tags->fetchEpisodeTags($this->episode['id']);
        $tag_list = array();
        foreach ($tags as $t) {
            $tag_list[] = $t['name'];
        }
        $this->episode['tags'] = implode(', ', $tag_list);
        
        // Genres
        $tags = $this->_model->episodes_genres->fetchEpisodeGenres($this->episode['id']);
        $tag_list = array();
        foreach ($tags as $t) {
            $tag_list[] = $t['name'];
        }
        $this->episode['genres'] = implode(', ', $tag_list);
        
        if ($this->_request->post('submit')) {
            $this->episode = array_merge($this->episode, $this->form_values);
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            // no errors, woot
            $this->_model->episodes->updateEpisode($this->episode['id'], $this->form_values);
            
            $tags = App_Util::tagsArray($this->_request->post('tags'));
            $this->_model->episodes_tags->updateEpisodeTags($this->episode['id'], $tags);
            
            $tags = App_Util::tagsArray($this->_request->post('genres'));
            $this->_model->episodes_genres->updateEpisodeGenres($this->episode['id'], $tags);
            
            $this->_setMessage('Episode Updated', 'Episode successfully updated.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons/' . $this->season['id'] . '/episodes');
        }
    }
    
    public function actionNewEpisode($id = null, $season_id = null)
    {
        $this->_checkPermission('admin_episodes_create');
        if (is_null($id)) {
            $this->_setError('Invalid Series ID', 'No series id was given.');
            $this->_redirect('/admin/series');
        }
        
        $this->series = $this->_model->series->fetchById($id);
        if (!$this->series) {
            $this->_setError('Invalid Series ID', 'That is not a valid series id.');
            $this->_redirect('/admin/series');
        }
        
        if (is_null($season_id)) {
            $this->_setError('Invalid Season ID', 'No season id was given.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        $this->season = $this->_model->seasons->fetchById($season_id);
        if (!$this->season) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        if ($this->season['series_id'] != $this->series['id']) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        $this->page_title .= ' - Create Episode';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            // no errors, woot
            $id = $this->_model->episodes->createEpisode($this->series['id'], $this->season['id'], $this->form_values);
            
            $tags = App_Util::tagsArray($this->_request->post('tags'));
            $this->_model->episodes_tags->updateEpisodeTags($id, $tags);
            
            $tags = App_Util::tagsArray($this->_request->post('genres'));
            $this->_model->episodes_genres->updateEpisodeGenres($id, $tags);
            
            $this->_setMessage('Episode Created', 'Episode successfully created.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons/' . $this->season['id'] . '/episodes');
        }
    }
    
    public function actionEpisodes($id = null, $season_id = null)
    {
        $this->_checkPermission('admin_episodes_view');
        $this->page_title .= ' - Manage Episodes';
        
        if (is_null($id)) {
            $this->_setError('Invalid Series ID', 'No series id was given.');
            $this->_redirect('/admin/series');
        }
        
        $this->series = $this->_model->series->fetchById($id);
        if (!$this->series) {
            $this->_setError('Invalid Series ID', 'That is not a valid series id.');
            $this->_redirect('/admin/series');
        }
        
        if (is_null($season_id)) {
            $this->_setError('Invalid Season ID', 'No season id was given.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        $this->season = $this->_model->seasons->fetchById($season_id);
        if (!$this->season) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        if ($this->season['series_id'] != $this->series['id']) {
            $this->_setError('Invalid Season ID', 'That is not a valid season id.');
            $this->_redirect('/admin/series/' . $this->series['id'] . '/seasons');
        }
        
        if ($this->_request->post('process')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            foreach ($this->_request->post('selected') as $id) {
                if ($this->_request->post('action') == 'delete' && $this->_hasPermission('admin_episodes_delete')) {
                    $this->_setError('Unallowed action', 'This action can have far reaching effects, please talk to an administrator to delete this.');
                    //$this->_model->users->deleteById($id);
                }
            }
        }
        
        $this->episodes = $this->_model->episodes->fetchEpisodesBySeriesIdAndSeasonId($this->series['id'], $this->season['id']);
    }
    
    public function actionUsers()
    {
        $this->_checkPermission('admin_users_view');
        $this->page_title .= ' - Manage Users';
        
        if ($this->_request->post('process')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired, please reload the page to continue with this form.');
                return;
            }

            foreach ($this->_request->post('selected') as $id) {
                if ($this->_request->post('action') == 'delete' && $this->_hasPermission('admin_users_delete')) {
                    $this->_model->users->deleteById($id);
                }
            }
        }
        
        $this->users = $this->_model->users->fetchUsers();
    }
    
    public function actionNewSeries()
    {
        $this->_checkPermission('admin_series_create');
        $this->page_title .= ' - Create Series';
        $this->form_values = $this->_request->post;
        $this->languages = $this->_model->languages->fetchLanguages();
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            $title = $this->_request->post('title');
            if (!$title || trim($title) == '') {
                $this->_setError('Title Left Blank', 'Title cannot be blank.');
                return;
            }

            // no errors, woot
            $id = $this->_model->series->createSeries($this->form_values);
            
            $tags = App_Util::tagsArray($this->_request->post('tags'));
            $this->_model->series_tags->updateSeriesTags($id, $tags);
            
            $tags = App_Util::tagsArray($this->_request->post('genres'));
            $this->_model->series_genres->updateSeriesGenres($id, $tags);
            
            $this->_model->series_taglines->updateTaglines($id, $this->_request->post('taglines'));
            
            $this->_setMessage('Series Created', 'Series successfully created.');
            $this->_redirect('/admin/series');
        }
    }
    
    public function actionEditSeries($id = null)
    {
        $this->_checkPermission('admin_series_edit');
        if (is_null($id)) {
            $this->_setError('Invalid Series ID', 'No series id was given.');
            $this->_redirect('/admin/series');
        }
        
        $this->series = $this->_model->series->fetchById($id);
        if (!$this->series) {
            $this->_setError('Invalid Series ID', 'That is not a valid series id.');
            $this->_redirect('/admin/series');
        }
        
        $this->page_title .= ' - Edit Series';
        $this->form_values = $this->_request->post;
        $this->languages = $this->_model->languages->fetchLanguages();
        
        // Tags
        $tags = $this->_model->series_tags->fetchSeriesTags($this->series['id']);
        $tag_list = array();
        foreach ($tags as $t) {
            $tag_list[] = $t['name'];
        }
        $this->series['tags'] = implode(', ', $tag_list);
        
        // Genres
        $tags = $this->_model->series_genres->fetchSeriesGenres($this->series['id']);
        $tag_list = array();
        foreach ($tags as $t) {
            $tag_list[] = $t['name'];
        }
        $this->series['genres'] = implode(', ', $tag_list);
        
        $this->series['taglines'] = $this->_model->series_taglines->fetchBySeriesId($this->series['id']);
        
        
        if ($this->_request->post('submit')) {
            $this->series = array_merge($this->series, $this->form_values);
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            $title = $this->_request->post('title');
            if (!$title || trim($title) == '') {
                $this->_setError('Title Left Blank', 'Title cannot be blank.');
                return;
            }

            // no errors, woot
            $this->_model->series->updateSeries($this->series['id'], $this->form_values);
            
            $tags = App_Util::tagsArray($this->_request->post('tags'));
            $this->_model->series_tags->updateSeriesTags($this->series['id'], $tags);
            
            $tags = App_Util::tagsArray($this->_request->post('genres'));
            $this->_model->series_genres->updateSeriesGenres($this->series['id'], $tags);
            
            $this->_model->series_taglines->updateTaglines($this->series['id'], $this->_request->post('taglines'));
            
            $this->_setMessage('Series Updated', 'Series successfully updated.');
            $this->_redirect('/admin/series');
        }
    }
    
    public function actionNewUser()
    {
        $this->_checkPermission('admin_users_create');
        $this->page_title .= ' - Create User';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            $username = $this->_request->post('username');
            if (!$username || trim($username) == '') {
                $this->_setError('Username Left Blank', 'Username cannot be blank.');
                return;
            }
            
            if (!preg_match("/^[a-z][-_a-z0-9]*$/i", $username)) { 
                $this->_setError('Invalid Username', "Usernames must start with a letter, and contain only letters, numbers, '-', and '_'.");
                return;
            }
            
            if (strlen($username) > 25 || strlen($username) < 3) { 
                $this->_setError('Invalid Username', "Usernames cannot be more than 25 characters and should be at least 3 characters long.");
                return;
            }
            
            $email = trim($this->_request->post('email'));
            
            if (!$email || $email == '') {
                $this->_setError('E-mail Left Blank', 'User e-mail cannot be blank.');
                return;
            }
            
            $user_from_email = $this->_model->users->fetchUserByEmail($email);
            if (!empty($user_from_email)) {
                $this->_setError('E-mail Address Already In Use', 'The email address you provided is already being used by another account.');
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->_setError('Invalid E-mail Address', 'The e-mail address you provided is invalid.');
                return;
            }
            
            if (!$this->_request->post('password') || trim($this->_request->post('password')) == '') {
                $this->_setError('Password Left Blank', 'Password cannot be blank.');
                return;
            }
            
            if (strlen($this->_request->post('password')) < 8) {
                $this->_setError('Password Length', 'Your password must be at least 8 characters in length.');
                return;
            }

            // no errors, woot
            $this->_model->users->createUser($this->_request->post('username'), $email, trim($this->_request->post('password')), (int) $this->_request->post('group_id'));
            $this->_setMessage('User Created', 'User successfully created.');
            $this->_redirect('/admin/users');
        }
        
        $this->groups = $this->_model->groups->fetchGroups();
    }
    
    public function actionEditUser($id = null)
    {
        $this->_checkPermission('admin_users_edit');
        if (is_null($id)) {
            $this->_setError('Invalid User ID', 'No user id was given.');
            $this->_redirect('/admin/users');
        }
        
        $this->user = $this->_model->users->fetchById($id);
        if (!$this->user) {
            $this->_setError('Invalid User ID', 'That is not a valid user id.');
            $this->_redirect('/admin/users');
        }
        
        $this->page_title .= ' - Edit User';
        $this->form_values = $this->_request->post;
        
        if ($this->_request->post('submit')) {
            // Check CSRF Token
            if (!$this->_checkToken($this->_request->post('csrf_token'))) {
                $this->_setError('CSRF Error', 'Your session has expired. Please reload the page to continue with this form.');
                return;
            }

            if (!$this->_request->post('username') || trim($this->_request->post('username')) == '') {
                $this->_setError('Username Left Blank', 'Username cannot be blank.');
                return;
            }
            
            $email = trim($this->_request->post('email'));
            
            if (!$email || $email == '') {
                $this->_setError('E-mail Left Blank', 'User e-mail cannot be blank.');
                return;
            }
            
            $user_from_email = $this->_model->users->fetchUserByEmail($email);
            if (!empty($user_from_email) && $this->user['email'] != $email) {
                $this->_setError('E-mail Address Already In Use', 'The email address you provided is already being used by another account.');
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->_setError('Invalid E-mail Address', 'The e-mail address you provided is invalid.');
                return;
            }

            // no errors, woot
            $this->_model->users->updateUser($this->user['id'], $this->form_values);
            $this->_setMessage('User Updated', 'User successfully updated.');
            $this->_redirect('/admin/users');
        }
        
        $this->groups = $this->_model->groups->fetchGroups();
    }
}
