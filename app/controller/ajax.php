<?php
/**
 * AJAX
 *
 */
class App_Controller_Ajax extends App_Controller_Base
{
    protected $_action_default = 'index';

    public $error = null;
    public $message = null;
    public $success = false;
    public $data = array();

    protected function _notFound($action, $params = null)
    {
        $this->_response->setStatusCode(404);
        $this->error = 'Not Found';
        $this->message = $action . ' does not exist.';
    }

    protected function _postAction()
    {
        $this->_view = null;
        $this->_layout = null;
        $this->_format = 'json';

        $ret = array(
            'success' => $this->success,
            'message' => $this->message,
            'error'   => $this->error,
            'data'    => $this->data,
        );

        $this->_response->content = json_encode($ret);
    }
    
    
    public function actionSeasonsBySeries()
    {
        $this->data = $this->_model->seasons->fetchSeasonsBySeriesId($this->_request->get('series_id'));
        $this->success = true;
    }
    
    public function actionEpisodesBySeason()
    {
        $this->data = $this->_model->episodes->fetchEpisodesBySeasonId($this->_request->get('season_id'));
        $this->success = true;
    }
    
    public function actionTimeline()
    {
        $resolution = $this->_request->get('resolution');
        $video = $this->_model->videos->fetchVideoById($this->_request->get('video_id'));
        $events = $this->_model->events->fetchByVideoId($this->_request->get('video_id'));
        $modules = $this->_model->modules->fetchModules();

        $view = new SolarLite_View();
        $view->assign(array(
            'video' => $video,
            'resolution' => $resolution,
            'events' => $events,
            'modules' => $modules
        ));
        $view->setTemplatePath(SolarLite::$system . '/app/controller/admin/view/');
        $this->data['html'] = $view->fetch('timelinectrl');
        $this->success = true;
    }
    
    public function actionNewVideoEvent()
    {
        $video_id = $this->_request->post('video_id');
        $module_id = $this->_request->post('module_id');
        $start_sec = $this->_request->post('start_sec');
        $resolution = $this->_request->post('resolution');
        if ($resolution == 0) {
            $end_sec = $start_sec + 1;
        } elseif ($resolution == 1) {
            $end_sec = $start_sec + 60;
        } elseif ($resolution == 2) {
            $end_sec = $start_sec + 3600;
        }
        
        $this->data['event_id'] = $this->_model->events->newVideoEvent($video_id, $module_id, $start_sec, $end_sec);
        $this->success = true;
    }
    
    public function actionUpdateVideoEvent()
    {
        $event_id = $this->_request->post('event_id');
        $event = $this->_model->events->fetchById($event_id);

        $start_sec = $this->_request->post('start_sec');
        $resolution = $this->_request->post('resolution');
        $end_sec = $start_sec + $event['duration'];
        
        $this->_model->events->updateVideoEvent($event_id, $start_sec, $end_sec);
        $this->success = true;
    }
    
    public function actionUpdateVideoEventOptions()
    {
        $event_id = $this->_request->post('event_id');
        $event = $this->_model->events->fetchById($event_id);
        $values = $this->_request->post('form');
        parse_str($values, $data);
        
        if (isset($data['duration'])) {
            $duration = $data['duration'];
            unset($data['duration']);
            $this->_model->events->updateVideoEvent($event_id, $event['start_second'], $event['start_second'] + $duration);
        }
        
        $this->_model->event_options->updateEventOptions($event_id, $data);
        $this->message = "Saved!";
        $this->success = true;
    }
    
    public function actionDeleteEvent()
    {
        $event_id = $this->_request->post('event_id');

        $this->_model->events->deleteById($event_id);
        $this->_model->event_options->deleteByEventId($event_id);

        $this->success = true;
    }
    
    public function actionUpdateEpisodeDescription()
    {
        $episode_id = $this->_request->post('episode_id');
        $desc = $this->_request->post('desc');
        if (trim($desc) != 'Edit description here') {
            $this->_model->episodes->updateDescription($episode_id, trim($desc));
        }
        $this->success = true;
    }
    
    public function actionPublishVideo()
    {
        $video_id = $this->_request->post('video_id');
        $video = $this->_model->videos->fetchById($video_id);
        if ($video['status'] == App_Model_Videos::$statuses['published']) {
            $this->_model->videos->updateStatus($video_id, App_Model_Videos::$statuses['active']);
            $this->data['published'] = false;
        } else {
            $this->_model->videos->updateStatus($video_id, App_Model_Videos::$statuses['published']);
            $this->data['published'] = true;
        }
        $this->success = true;
    }
    
    public function actionEventOptions()
    {
        $event_id = $this->_request->get('event_id');

        $event = $this->_model->events->fetchEventById($event_id);
        $options = $this->_model->event_options->fetchByEventId($event_id);

        $view = new SolarLite_View();
        $view->assign(array(
            'event' => $event,
            'options' => $options,
        ));
        $view->setTemplatePath(SolarLite::$system . '/app/controller/admin/view/');
        $this->data['html'] = $view->fetch('_' . $event['slug']);
        $this->success = true;
    }
}
