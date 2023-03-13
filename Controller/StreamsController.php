<?php
/**
 * user content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * user content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */

App::uses('Sanitize', 'Utility');
class StreamsController extends AppController {



/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Streams';

/**
 * This controller use a model admins
 *
 * @var array
 */
	public $uses 		= array('Stream');
	
	public $helpers 	= array('Html', 'Session','General','Csv');
	var $components = 	array('General',"Upload");
	
	
	public function beforeFilter() {
	
        parent::beforeFilter();
        $this->loadModel('Plan');
		$this->Auth->allow('stream_detail','add_sandbox_stream','add_stream','recorded_stream_detail','follow_ajax','get_all_recordings','add_schedule','upcoming_streams_listing','update_stream_unique_viewers','get_stream_unique_viewers_count','update_schedule_stream_staus_cron','paypal_notify_donate_stream','paypal_notify_donate_recorded_video');
    }

	
	
	/*
	@ param : null
	@ return void
	*/
	
	public function admin_index() {
		/* pr( $this->Session);
		die; */
		
		$this->loadModel('User');
		$grid_list_type = "";
		
		if (!isset($this->params['named']['page'])) {
            $this->Session->delete('AdminSearch');
        }
		
		$filters	=	array();
        if (!empty($this->request->data)) 
		{
			
			$grid_list_type = $this->request->data['Stream']['grid_list_type'];
			
			$this->Session->delete('AdminSearch');
           if (isset($this->request->data['Stream']['title']) && $this->request->data['Stream']['title'] != '') {
                $title = trim($this->request->data['Stream']['title']);
                $this->Session->write('AdminSearch.title', $title);
				
            }
			if (isset($this->request->data['Stream']['user_id']) && $this->request->data['Stream']['user_id'] != '') {
                $user_id = trim($this->request->data['Stream']['user_id']);
                $this->Session->write('AdminSearch.user_id', $user_id);
				
            }
		}
		
		if ($this->Session->check('AdminSearch')) 
		{
            $keywords 	= 	$this->Session->read('AdminSearch');
			foreach($keywords as $key=>$values){
				if($key == 'title')
				{
					$filters[] = array('Stream.'.$key.' LIKE'=>"%".$values."%");					
				}
				if($key == 'user_id')
				{
					$filters[] = array('Stream.'.$key=>$values);					
				}
			}
			
		}
		
		$this->Stream->bindModel(array('belongsTo'=>array('User')));
		/* pr($this->params);
		die; */
		$this->paginate = array('Stream' => array(
			'limit' =>Configure::read('App.PageLimit'),
			'order' => array('Stream.sorting' => 'ASC'),
			'conditions' => $filters,
        ));	
				
		$user_list = $this->User->find('list',array('fields'=>array('User.id','User.nickname'),'conditions'=>array('User.role_id'=>USER_ROLE)));
		$data = $this->paginate('Stream');		
		/* pr($data );
		die; */
		$this->set(compact('data','user_list','grid_list_type'));
		$this->set('title_for_layout', __('Live Streaming', true));
		
	}
	
	
	public function admin_upcoming() {
		/* pr( $this->Session);
		die; */
		
		$this->loadModel('User');
		$grid_list_type = "";
		
		if (!isset($this->params['named']['page'])) {
            $this->Session->delete('AdminSearch');
        }
		
		$filters	=	array();
		$filters	=	array('Stream.schedule_start_date > NOW()');
        if (!empty($this->request->data)) 
		{
			
			$grid_list_type = $this->request->data['Stream']['grid_list_type'];
			
			$this->Session->delete('AdminSearch');
           if (isset($this->request->data['Stream']['title']) && $this->request->data['Stream']['title'] != '') {
                $title = trim($this->request->data['Stream']['title']);
                $this->Session->write('AdminSearch.title', $title);
				
            }
			if (isset($this->request->data['Stream']['user_id']) && $this->request->data['Stream']['user_id'] != '') {
                $user_id = trim($this->request->data['Stream']['user_id']);
                $this->Session->write('AdminSearch.user_id', $user_id);
				
            }
		}
		
		if ($this->Session->check('AdminSearch')) 
		{
            $keywords 	= 	$this->Session->read('AdminSearch');
			foreach($keywords as $key=>$values){
				if($key == 'title')
				{
					$filters[] = array('Stream.'.$key.' LIKE'=>"%".$values."%");					
				}
				if($key == 'user_id')
				{
					$filters[] = array('Stream.'.$key=>$values);					
				}
			}
			
		}
		
		$this->Stream->bindModel(array('belongsTo'=>array('User')));
		
		$this->paginate = array('Stream' => array(
			'limit' =>Configure::read('App.PageLimit'),
			'order' => array('Stream.sorting' => 'ASC'),
			'conditions' => $filters,
        ));	
				
		$user_list = $this->User->find('list',array('fields'=>array('User.id','User.nickname'),'conditions'=>array('User.role_id'=>USER_ROLE)));
		$data = $this->paginate('Stream');		
		/* pr($data );
		die; */
		$this->set(compact('data','user_list','grid_list_type'));
		$this->set('title_for_layout', __('Upcoming Stream', true));
		
	}
	
	
	
	public function admin_live() {
		/* pr( $this->Session);
		die; */
		
		$this->loadModel('User');
		$grid_list_type = "";
		
		if (!isset($this->params['named']['page'])) {
            $this->Session->delete('AdminSearch');
        }
		
		$filters	=	array();
		$filters	=	array('Stream.stream_state'=>STARTED);
        if (!empty($this->request->data)) 
		{
			
			$grid_list_type = $this->request->data['Stream']['grid_list_type'];
			
			$this->Session->delete('AdminSearch');
           if (isset($this->request->data['Stream']['title']) && $this->request->data['Stream']['title'] != '') {
                $title = trim($this->request->data['Stream']['title']);
                $this->Session->write('AdminSearch.title', $title);
				
            }
			if (isset($this->request->data['Stream']['user_id']) && $this->request->data['Stream']['user_id'] != '') {
                $user_id = trim($this->request->data['Stream']['user_id']);
                $this->Session->write('AdminSearch.user_id', $user_id);
				
            }
		}
		
		if ($this->Session->check('AdminSearch')) 
		{
            $keywords 	= 	$this->Session->read('AdminSearch');
			foreach($keywords as $key=>$values){
				if($key == 'title')
				{
					$filters[] = array('Stream.'.$key.' LIKE'=>"%".$values."%");					
				}
				if($key == 'user_id')
				{
					$filters[] = array('Stream.'.$key=>$values);					
				}
			}
			
		}
		
		$this->Stream->bindModel(array('belongsTo'=>array('User')));
		
		$this->paginate = array('Stream' => array(
			'limit' =>Configure::read('App.PageLimit'),
			'order' => array('Stream.sorting' => 'ASC'),
			'conditions' => $filters,
        ));	
				
		$user_list = $this->User->find('list',array('fields'=>array('User.id','User.nickname'),'conditions'=>array('User.role_id'=>USER_ROLE)));
		$data = $this->paginate('Stream');		
		/* pr($data );
		die; */
		$this->set(compact('data','user_list','grid_list_type'));
		$this->set('title_for_layout', __('Live Streams', true));
		
	}
	
	
	/*
	@ param : null
	@ return void
	*/
	public function admin_detail($id = null){
	
		$this->set('title_for_layout','Streaming Detail');
		
		$this->Stream->id = $id;
		$this->set(compact('id'));
		
		/*check conditions allreday conditions for users update*/
        if (!$this->Stream->exists()) {
            throw new NotFoundException(__('Invalid Stream'));
        }
		
		$this->Stream->bindModel(array('belongsTo'=>array('User')));
		$data = $this->Stream->read(null, $id);
		$this->set(compact('data'));
	}
	
	
	public function admin_upcoming_view($id = null){
	
		$this->set('title_for_layout','Streaming Detail');
		
		$this->Stream->id = $id;
		$this->set(compact('id'));
		
		/*check conditions allreday conditions for users update*/
        if (!$this->Stream->exists()) {
            throw new NotFoundException(__('Invalid Stream'));
        }
		
		$this->Stream->bindModel(array('belongsTo'=>array('User')));
		$data = $this->Stream->read(null, $id);
		$this->set(compact('data'));
	}
	
	
	public function admin_live_view($id = null){
	
		$this->set('title_for_layout','Streaming Detail');
		
		$this->Stream->id = $id;
		$this->set(compact('id'));
		
		/*check conditions allreday conditions for users update*/
        if (!$this->Stream->exists()) {
            throw new NotFoundException(__('Invalid Stream'));
        }
		
		$this->Stream->bindModel(array('belongsTo'=>array('User')));
		$data = $this->Stream->read(null, $id);
		$this->set(compact('data'));
	}
	
	
	public function admin_featured($id = null) {
	$this->layout = false;
	$this->autoRender = false;
	$get_featured_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.featured')));
		if($get_featured_detail['Stream']['featured'] == 1)
		{
			$this->Stream->id = $get_featured_detail['Stream']['id'];
			$this->Stream->saveField('featured',0);
			die("0");
		}
		else
		{
			$this->Stream->id = $get_featured_detail['Stream']['id'];
			$this->Stream->saveField('featured',1);
			die("1");
		}
	}
	
	public function admin_change_featured_status($id = null) {
		
		$get_featured_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.featured')));
	
		if($get_featured_detail['Stream']['featured'] == 1)
		{
			$this->Stream->id = $get_featured_detail['Stream']['id'];
			$this->Stream->saveField('featured',0);
			 $this->Session->setFlash(__('Admin\'s Stream featured has been changed'), 'admin_flash_good');
				$this->redirect($this->referer());
		}
		else
		{
			$this->Stream->id = $get_featured_detail['Stream']['id'];
			$this->Stream->saveField('featured',1);
			$this->Session->setFlash(__('Admin\'s Stream unfeatured was not changed', 'admin_flash_error'));
			$this->redirect($this->referer());
		}
    }
	public function admin_status($id = null) {
        if ($this->Session->check('Auth.User.id') && $this->Session->read('Auth.User.role_id') == Configure::read('App.SubAdmin.role')) { die('iiififififi');
            $this->Session->setFlash(__('You are not authorizatized for this action'), 'admin_flash_error');
            $this->redirect(array('controller' => 'admins', 'action' => 'dashboard'));
        }
        $this->Stream->id = $id;
		
		
        if (!$this->Stream->exists()) {
            throw new NotFoundException(__('Invalid Stream'));
        }
       
        $this->loadModel('Stream'); 
        if ($this->Stream->toggleStatus($id)) { 
            $this->Session->setFlash(__('Stream status has been changed'), 'admin_flash_good');
		    $this->redirect($this->referer());
        }
        $this->Session->setFlash(__('Stream status was not changed', 'admin_flash_error'));
        $this->redirect($this->referer());
    }
	public function admin_process() {
	
		if (!empty($this->request->data)) {
			if (!isset($this->request->params['_Token']['key']) || ($this->request->params['_Token']['key'] != $this->request->params['_Token']['key'])) {
                $blackHoleCallback = $this->Security->blackHoleCallback;
                $this->$blackHoleCallback();
            }
			
          
            $action = $this->request->data['Stream']['pageAction'];
            foreach ($this->request->data['Stream'] AS $value) {
                if ($value != 0) {
                    $ids[] = $value;
                }
            }

			
            if (count($this->request->data) == 0 || $this->request->data['Stream'] == null) {
                $this->Session->setFlash('No items selected.', 'admin_flash_bad');
                  $this->redirect($this->referer());
            }
            if ($action == "activate") {
				
				$this->Stream->updateAll(array('status'=>Configure::read('App.Status.active')),array('Stream.id'=>$ids));
               
                $this->Session->setFlash('Stream have been activated successfully', 'admin_flash_good');
                $this->redirect($this->referer());
            }
			
            if ($action == "deactivate") {
			
				$this->Stream->updateAll(array('status'=>Configure::read('App.Status.inactive')),array('Stream.id'=>$ids));
				
                $this->Session->setFlash('Streams have been deactivated successfully', 'admin_flash_good');
				 $this->redirect($this->referer());
            }
			
        } else {
            $this->redirect($this->referer());
        }
    }
	
	
	public function admin_delete($id = null) {
		$this->layout = false;
		// $this->autoRender = false;
		$this->loadModel('RecordingStream');
		$this->loadModel('Message');
		$this->loadModel('ChannelFollower');
		$this->loadModel('ChannelSubscription');
		$get_stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id)));
		if(!empty($get_stream_detail))
		{			
		
			if($get_stream_detail['Stream']['stream_image'] &&  file_exists(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$get_stream_detail['Stream']['stream_image'] )) 
			{
				@unlink(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$get_stream_detail['Stream']['stream_image']);
			}
			
			if(!empty($get_stream_detail['Stream']['stream_key']))
			{
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$get_stream_detail['Stream']['stream_key'].'/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
				'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); 
				$result = curl_exec($ch);
				curl_close($ch);
			}
			if(!empty($get_stream_detail['Stream']['schedule_id']))
			{
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/schedules/'.$get_stream_detail['Stream']['schedule_id'].'/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
				'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); 
				$result = curl_exec($ch);
				curl_close($ch);
			}
			$this->Stream->deleteAll(array('Stream.id' => $id));
			$this->Message->deleteAll(array('Message.stream_id' => $id));
			$this->ChannelFollower->deleteAll(array('ChannelFollower.stream_id' => $id));
			$this->ChannelSubscription->deleteAll(array('ChannelSubscription.stream_id' => $id));
			
			$RecordingStream = $this->RecordingStream->find('all',array('conditions'=>array('RecordingStream.stream_id'=>$id)));	
			if(!empty($RecordingStream))
			{
				foreach($RecordingStream as $recording_key=>$recording_value)
				{
				
					if($recording_value['RecordingStream']['image'] &&  file_exists(WWW_ROOT.RECORDING_IMAGE_FULL_DIR.DS.$recording_value['RecordingStream']['image'] )) 
					{
						@unlink(WWW_ROOT.RECORDING_IMAGE_FULL_DIR.DS.$recording_value['RecordingStream']['image']);
					}
					
					$ch = curl_init('https://api.cloud.wowza.com/api/v1/recordings/'.$recording_value['RecordingStream']['recording_key'].'/');
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
					'Content-Type: application/json ;charset=utf-8', 
						'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
						'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
						)                                                                       
					); 
					$result = curl_exec($ch);
					curl_close($ch);
					
				}
				$this->RecordingStream->deleteAll(array('RecordingStream.user_id' => $id));
				$this->Session->setFlash('Record has been deleted successfully','admin_flash_good');
				$this->redirect($this->referer());
				
			}
		}
	}
	
	public function admin_slider_index() {
		
		
		if (!isset($this->params['named']['page'])) {
            $this->Session->delete('AdminSearch');
        }
		$this->loadModel('Stream');
		$filters	=	array();
        if (!empty($this->request->data)) {
			$this->Session->delete('AdminSearch');
           if (isset($this->request->data['Stream']['title']) && $this->request->data['Stream']['title'] != '') {
                $title = trim($this->request->data['Stream']['title']);
                $this->Session->write('AdminSearch.title', $title);
            }
			
           if (isset($this->request->data['Stream']['stream_bio']) && $this->request->data['Stream']['stream_bio'] != '') {
                $stream_bio = trim($this->request->data['Stream']['stream_bio']);
                $this->Session->write('AdminSearch.stream_bio', $stream_bio);
            }
			
           if (isset($this->request->data['Channel']['name']) && $this->request->data['Channel']['name'] != '') {
                $name = trim($this->request->data['Channel']['name']);
                $this->Session->write('AdminSearch.name', $name);
            }
			
		}
		
		if ($this->Session->check('AdminSearch')) {
            $keywords 	= 	$this->Session->read('AdminSearch');
			foreach($keywords as $key=>$values){
				if($key == 'title'){
					$filters[] = array('Stream.'.$key.' LIKE'=>"%".$values."%");					
				}
				if($key == 'stream_bio'){
					$filters[] = array('Stream.'.$key.' LIKE'=>"%".$values."%");				
				}
				if($key == 'name'){
					$filters[] = array('Channel.'.$key.' LIKE'=>"%".$values."%");				
				}
				
				
			}
		}
		
		$filters[] = array('Stream.is_home'=>1);		
		
		
		$this->Stream->bindModel(array(
			'belongsTo'=>array(
				'User'=>array(
					'className'=>'User',
					'foreignKey'=>'user_id',
					'fields'=>array('id','first_name','profile_image')	
				),
				'Channel'=>array(
					'className'=>'Channel',
					'foreignKey'=>'channel_id',
					'fields'=>array('id','name','image')	
				)
			)			
		),false);
		
		$this->loadModel('Stream');		
		$this->paginate = array('Stream' => array(
			'limit' =>Configure::read('App.PageLimit'),
			'order' => array('Stream.sorting' => 'ASC'),
			'conditions' => $filters,
        ));
		$data = $this->paginate('Stream');		
		$this->set(compact('data'));
		$this->set('title_for_layout', __('Home Slider Listing', true));
		
	}
	public function admin_slider_view($id = null){
	
		$this->set('title_for_layout','View Detail');
		
		$this->loadModel('Stream');
		$this->Stream->id = $id;
		$this->set(compact('id'));
		
		/*check conditions allreday conditions for users update*/
        if (!$this->Stream->exists()) {
            throw new NotFoundException(__('Invalid Video'));
        }
		$this->Stream->bindModel(array(
			'belongsTo'=>array(
				'User'=>array(
					'className'=>'User',
					'foreignKey'=>'user_id',
					'fields'=>array('id','first_name','profile_image')	
				),
				'Channel'=>array(
					'className'=>'Channel',
					'foreignKey'=>'channel_id',
					'fields'=>array('id','name','image','play_count')	
				)
			)			
		),false);
		$data = $this->Stream->read(null, $id);
		$this->set(compact('data'));
	}
	
	public function admin_slider_edit($id = null){
	
		$this->set('title_for_layout','Edit Slider');		
		$this->loadModel('Stream');
		$this->Stream->id 	= 	$id;
		
		/*check conditions all ready conditions for users update*/
        if (!$this->Stream->exists()) {
            throw new NotFoundException(__('Invalid Recording Stream'));
        }
		/*form post and check conditions*/
		if ($this->request->is('post') || $this->request->is('put')) {
			
			if (!empty($this->request->data)) {
				
				if (!isset($this->request->params['_Token']['key']) || ($this->request->params['_Token']['key'] != $this->request->params['_Token']['key'])) {
					$blackHoleCallback = $this->Security->blackHoleCallback;
					$this->$blackHoleCallback();
				}
				//validate user data
				$this->Stream->set($this->request->data['Stream']);
				$this->Stream->setValidation('edit');
				if ($this->Stream->validates()) {
					$this->Stream->create();				
				if ($this->Stream->save($this->request->data['Stream'],false)) {					
					$recording_stream_id	=	$id;					
					$p_image	=	$this->request->data['Stream']['recording_stream_image'];
					$old_image	=	$this->request->data['Stream']['stream_image'];
										
					if (!empty($p_image) && $p_image['tmp_name'] != '' && $p_image['size'] > 0) {
			
						$allowed	=	array('jpg','jpeg','png');
						$temp 		= 	explode(".", $p_image["name"]);
						$extension 	= 	end($temp);
						$imageName 	= 	'recording_stream_image_'.microtime(true).'.'.$extension;
						$files		=	$p_image;
						
						$result 	= 	$this->Upload->upload($files, WWW_ROOT . STREAM_IMAGE_FULL_DIR . DS, $imageName,'',$allowed);
						
						if(!empty($this->Upload->result) && empty($this->Upload->errors)) {
							
							if($old_image &&  file_exists(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$old_image )) {
								@unlink(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$old_image);
							}
							
							$this->Stream->id	=	$recording_stream_id;
							$this->Stream->saveField('stream_image',$imageName,false);
						}
						
					}
					
					$this->Session->setFlash("Record has been added successfully", 'admin_flash_good');
					$this->redirect(array('controller'=>'streams', 'action'=>'slider_index'));
			
				} 
			} else {
					 $this->Session->setFlash(__('The record could not be saved. Please, try again.', true), 'admin_flash_bad');
                }
			}
        } else {
			$this->request->data = $this->Stream->read(null, $id);	
// pr($this->request->data);die;			
			
		}
	}
	
	
	public function admin_change_ishome_status($id = null) {
		
		$this->loadModel('Stream');
		$get_ishome_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.is_home')));
	
	
		if($get_ishome_detail['Stream']['is_home'] == 1)
		{
			$this->Stream->id = $get_ishome_detail['Stream']['id'];
			$this->Stream->saveField('is_home',0);
			 $this->Session->setFlash(__('Stream does not show on home page'), 'admin_flash_good');
				$this->redirect($this->referer());
		}
		else
		{
			$this->Stream->id = $get_ishome_detail['Stream']['id'];
			$this->Stream->saveField('is_home',1);
			$this->Session->setFlash(__('Stream show on home page'), 'admin_flash_good');
			$this->redirect($this->referer());
		}
    }
	
	public function admin_change_video_button_status($id = null) {
		
		$this->loadModel('Stream');
		$get_video_button_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.video_play_button_type')));
	
	
		if($get_video_button_detail['Stream']['video_play_button_type'] == 1)
		{
			$this->Stream->id = $get_video_button_detail['Stream']['id'];
			$this->Stream->saveField('video_play_button_type',0);
			$this->Session->setFlash(__('Slider video show start button'), 'admin_flash_good');
			$this->redirect($this->referer());
		}
		else
		{
			$this->Stream->id = $get_video_button_detail['Stream']['id'];
			$this->Stream->saveField('video_play_button_type',1);
			$this->Session->setFlash(__('Slider video auto play'), 'admin_flash_good');
			$this->redirect($this->referer());
		}
    }
	public function admin_video_status($id = null) {
        if ($this->Session->check('Auth.User.id') && $this->Session->read('Auth.User.role_id') == Configure::read('App.SubAdmin.role')) { die('iiififififi');
            $this->Session->setFlash(__('You are not authorizatized for this action'), 'admin_flash_error');
            $this->redirect(array('controller' => 'admins', 'action' => 'dashboard'));
        }
		$this->loadModel('Stream');
        $this->Stream->id = $id;
		
		
        if (!$this->Stream->exists()) {
            throw new NotFoundException(__('Invalid Recorded stream'));
        }
       
        $this->loadModel('RecordingStream'); 
        if ($this->Stream->toggleStatus($id)) { 
            $this->Session->setFlash(__('Stream status has been changed'), 'admin_flash_good');
		    $this->redirect($this->referer());
        }
        $this->Session->setFlash(__('Stream status was not changed', 'admin_flash_error'));
        $this->redirect($this->referer());
    }
	
	
	
	
	
	
	
	
	public function channel_listing()
	{
		$this->layout = "front";
		$this->set('title_for_layout','CHANNELS');
		$this->loadModel('Channel');
		$channel_listing = $this->Channel->find('all',array('conditions'=>array('Channel.status'=>Configure::read('App.Status.active'))));
		$this->set('channel_listing',$channel_listing);
		/* pr($channel_listing);
		die; */
		
	}
	
	
	
	
	public function channel_detail($id = null)
	{
		$this->layout = "lay_channel_detail";
		$this->set('title_for_layout','Channel Detail');
		
		$this->loadModel('Channel');
		$this->loadModel('RecordingStreams');
		$this->Channel->bindModel(array(
			'belongsTo'=>array(
				'User'=>array(
					'className'=>'User',
					'foreignKey'=>'user_id',
					'fields'=>array('id','first_name','profile_image')	
				)
			)			
		),false);
		$this->Channel->bindModel(array(
			'hasMany'=>array(
				'RecordingStreams'=>array(
					'className'=>'RecordingStreams',
					'foreignKey'=>'channel_id',
					'fields'=>array('id','title')	
				)
			)			
		),false);
		
		$channelData = $this->Channel->find('first',array('conditions'=>array('Channel.id'=>$id)));
		$this->set('channelData',$channelData);
		
	}
	
	
	
	
	
	public function stream_detail($id)
	{
		$this->layout = 'lay_stream_detail';
		$this->set('title_for_layout','Stream Detail');
		$this->loadmodel("ChannelSubscription");
		$this->loadModel('Message');
		$this->loadModel('ChannelFollower');
		$this->loadModel('RecordingStream');
		
		$this->Message->bindModel(array(
			'belongsTo'=>array(
				'User'=>array(
					'className'=>'User',
					'foreignKey'=>'sender_id',
					'fields'=>array('User.profile_image','User.nickname')
				)
			)
		),false);
		
		
		$this->Stream->bindModel(array(
                'hasOne' => array(
                    'ChannelFollower' => array(
                        'className' => 'ChannelFollower',
                        'foreignKey' => 'stream_id',
                        'fields' => array('is_follow'), 
						'conditions'=>array('ChannelFollower.user_id'=>$this->Auth->user('id'),'ChannelFollower.recording_stream_id'=>'0'),
                    ),
				),	
				'hasMany'=>array(
					'Message'=>array(
						'className'=>'Message',
						'foreignKey'=>'stream_id',
						'conditions'=>array('Message.recording_stream_id'=>0),
						'order'=>array('Message.created'=>'Asc')
					)
					
                ),
				'belongsTo'=>array(
					'Channel','User'
				)	
			), false
		);
		
		$user_id = $this->Auth->User('id');
		$user_detail = $this->User->find('first', array('conditions' => array('User.id' => $user_id),'fields'=>array('User.profile_image','User.nickname')));
		
		
		$stream_detail = $this->Stream->find('first',array('recursive'=>2,'conditions'=>array('Stream.id'=>$id)));
		// pr($stream_detail);die;
		$channel_subscribe_check_user = $this->ChannelSubscription->find('count',array('conditions'=>array('ChannelSubscription.user_id'=>$this->Auth->user('id'),'ChannelSubscription.channel_id'=>$stream_detail['Channel']['id'],'ChannelSubscription.stream_id'=>$stream_detail['Stream']['id'])));
		
		
		$total_unique_viewers = $this->get_stream_unique_viewers_count($stream_detail['Stream']['stream_key']);
		$this->set('total_unique_viewers',$total_unique_viewers);
		
		
		$related_recorded_stream_listing = $this->RecordingStream->find('all',array('conditions'=>array('RecordingStream.stream_id'=>$id)));
		
		$streamCountData	=	$this->ChannelFollower->find('count',array('conditions'=>array('ChannelFollower.stream_id'=>$id,'ChannelFollower.is_follow'=>1)));		
		$this->set('streamCountData',$streamCountData);
		
		
		$this->set('related_recorded_stream_listing',$related_recorded_stream_listing);
		$this->set('stream_detail',$stream_detail);
		$this->set('user_detail',$user_detail);
		$this->set('channel_subscribe_check_user',$channel_subscribe_check_user);
		
	}
	
	
	public function follow_ajax() {
		if (!empty($this->request->data)) {
			$this->loadModel('Channel');				
			$this->loadModel('ChannelFollower');				
			$follow_status = '';
			if(!empty($this->request->data['recording_stream_id'])){
				$lkD = $this->ChannelFollower->find('first', array('conditions' => array('ChannelFollower.user_id' => $this->request->data['user_id'], 'ChannelFollower.recording_stream_id' => $this->request->data['recording_stream_id'])));
				if (empty($lkD)) {
					$followData = array('user_id' => $this->request->data['user_id'],
						'stream_id' => $this->request->data['stream_id'],
						'recording_stream_id' => $this->request->data['recording_stream_id'],
						'channel_id' => $this->request->data['channel_id'],
						'is_follow' => $this->request->data['status']
					);
				} else {
					$followData = array('user_id' => $this->request->data['user_id'],
						'stream_id' => $this->request->data['stream_id'],
						'recording_stream_id' => $this->request->data['recording_stream_id'],
						'channel_id' => $this->request->data['channel_id'],
						'is_follow' => $this->request->data['status'],
						'id' => $lkD['ChannelFollower']['id']
					);
				}
			
			}else{
			
				$lkD = $this->ChannelFollower->find('first', array('conditions' => array('ChannelFollower.user_id' => $this->request->data['user_id'], 'ChannelFollower.stream_id' => $this->request->data['stream_id'], 'ChannelFollower.recording_stream_id' =>'0')));
				
				
				//if($this->request->data['status']);die;
				if (empty($lkD)) {
					$followData = array('user_id' => $this->request->data['user_id'],
						'stream_id' => $this->request->data['stream_id'],						
						'channel_id' => $this->request->data['channel_id'],
						'is_follow' => $this->request->data['status']
					);
				} else {
					$followData = array('user_id' => $this->request->data['user_id'],
						'stream_id' => $this->request->data['stream_id'],						
						'channel_id' => $this->request->data['channel_id'],
						'is_follow' => $this->request->data['status'],
						'id' => $lkD['ChannelFollower']['id']
					);
				}
				
			}
			
			
			if ($this->ChannelFollower->saveAll($followData)) {
				
				$channelData	=	$this->Channel->find('first',array('fields'=>array('id','follower_count'),'conditions'=>array('Channel.id'=>$this->request->data['channel_id'])));
				
				if(!empty($channelData)){
					$this->Channel->id	=	$channelData['Channel']['id'];				
					
					
					if(isset($this->request->data['status']) && $this->request->data['status'] == 0 && $channelData['Channel']['follower_count'] > 0) {
						$this->Channel->saveField('follower_count',$channelData['Channel']['follower_count']-1,false);
					} else {
						$this->Channel->saveField('follower_count',$channelData['Channel']['follower_count']+1,false);					
					}
				}
				
				
				if(!empty($this->request->data['recording_stream_id'])){
					$streamCountData	=	$this->ChannelFollower->find('count',array('conditions'=>array('ChannelFollower.recording_stream_id'=>$this->request->data['recording_stream_id'],'ChannelFollower.is_follow'=>1)));
				}else{
					$streamCountData	=	$this->ChannelFollower->find('count',array('conditions'=>array('ChannelFollower.stream_id'=>$this->request->data['stream_id'],'ChannelFollower.is_follow'=>1)));
				}
				
				
				
				$response = array(
					'message' => 'success',
					'success' => '1',
					'msg' => 'You have follow this post successfully.',
					'count'=> $streamCountData		
				);
				
			} else {
				$response = array(
					'message' => 'Failed',
					'success' => '0',
					'msg' => 'Error! try again',
					'count' => ''
				);
			}
		} else {
			$response = array(
				'message' => 'Failed',
				'msg' => 'Error! try again',
				'count' => ''
			);
		}
	    $response = array('response' => $response);
	    die(json_encode($response));
	}
	
	
	public function add_stream()
	{
		$this->layout = "lay_stream_detail";
		$this->set('title_for_layout','Add Stream');
		/* $add_stream_json_string = '{
		  "live_stream": {
			"name": "My Live Stream",
			"transcoder_type": "transcoded",
			"billing_mode": "pay_as_you_go",
			"broadcast_location": "eu_germany",
			"recording": true,
			"closed_caption_type": "none",
			"encoder": "wowza_gocoder",
			"delivery_method": "push",
			"delivery_type": "single-bitrate",
			"delivery_protocol": "hls-hds",
			"use_stream_source": false,
			"aspect_ratio_width": 1920,
			"aspect_ratio_height": 1080
		  }
		}';
		
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_json_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:CoNSH9TCP70HJpRWSe3wgqMWOdFvccittBuxuwPeMuQC0omrWS8aSCgHohLL3518',
			'wsc-access-key:5VVa5ClrSQVtWuEiprQol5QstTgroismLQNsVroP5IZdqoyLrnuWdxgvGcO8385c'
			)                                                                       
		);                                                                                                                   
																															 
		$result = curl_exec($ch);
		curl_close($ch);
		echo $result; */
		
		
		/* $response = '{
	"live_stream": {
		"id": "29g9s54w",
		"name": "My Live Stream",
		"transcoder_type": "transcoded",
		"billing_mode": "pay_as_you_go",
		"broadcast_location": "eu_germany",
		"recording": true,
		"closed_caption_type": "none",
		"encoder": "wowza_gocoder",
		"delivery_method": "push",
		"delivery_protocol": "hls-hds",
		"use_stream_source": false,
		"aspect_ratio_width": 1920,
		"aspect_ratio_height": 1080,
		"connection_code": "020531",
		"connection_code_expires_at": "2016-07-12T10:36:55.000Z",
		"source_connection_information": {
			"primary_server": "78a4e8.entrypoint.cloud.wowza.com",
			"host_port": 1935,
			"application": "app-9c70",
			"stream_name": "cceecfa9",
			"disable_authentication": false,
			"username": "client11469",
			"password": "edf107a4"
		},
		"video_fallback": false,
		"player_id": "ypgybrff",
		"player_responsive": false,
		"player_width": 640,
		"player_countdown": false,
		"player_embed_code": "in_progress",
		"player_hds_playback_url": "http://wowzaprodhd68-lh.akamaihd.net/z/20aeccce_1@42948/manifest.f4m",
		"player_hls_playback_url": "http://wowzaprodhd68-lh.akamaihd.net/i/20aeccce_1@42948/master.m3u8",
		"hosted_page": true,
		"hosted_page_title": "My Live Stream",
		"hosted_page_url": "in_progress",
		"hosted_page_sharing_icons": true,
		"stream_targets": [{
			"id": "4n9z7m1v"
		}],
		"created_at": "2016-07-11T10:36:55.000Z",
		"updated_at": "2016-07-11T10:36:56.000Z",
		"links": [{
			"rel": "self",
			"method": "GET",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w"
		}, {
			"rel": "update",
			"method": "PATCH",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w"
		}, {
			"rel": "state",
			"method": "GET",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/state"
		}, {
			"rel": "thumbnail_url",
			"method": "GET",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/thumbnail_url"
		}, {
			"rel": "start",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/start"
		}, {
			"rel": "reset",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/reset"
		}, {
			"rel": "stop",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/stop"
		}, {
			"rel": "regenerate_connection_code",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/regenerate_connection_code"
		}, {
			"rel": "delete",
			"method": "DELETE",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w"
		}]
	}
	}'; */
		die("hiiiiii");
		
	}
	
	
	
	public function test()
	{
			
			
		/* $ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/pfzwlljt/thumbnail_url/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$stream_viewers_response_array = json_decode($result,true);
		pr($stream_viewers_response_array); */

		$current_time = date('Y-m-d H:i:s');
		//$five_minute_ago_time = date('Y-m-d H:i:s',(time() - 300));
		echo 'https://api.cloud.wowza.com/api/v1/usage/viewer_data/stream_targets/hjlttcpb/';
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/usage/viewer_data/stream_targets/hjlttcpb/');
		//$get_stream_viewers = '{"from":"'.$current_time.'","to":"'.$five_minute_ago_time.'"}'; 
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");     
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $get_stream_viewers);     
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$stream_viewers_response_array = json_decode($result,true);
		pr($stream_viewers_response_array);
					
		die;			
	
		$player_logo = SITE_URL.'img/Front/logo.png';
		$add_stream_request = '{
				  "live_stream": {
					"name": "test111",
					"transcoder_type": "transcoded",
					"billing_mode": "pay_as_you_go",
					"broadcast_location": "eu_belgium",
					"recording":"true",
					"encoder": "other_rtmp",
					"delivery_method": "push",
					"disable_authentication":"true",
					"aspect_ratio_width": 1024,
					"aspect_ratio_height": 576,
					"player_responsive": "true",
					"player_countdown": "true",
					"hosted_page": "true",
					"hosted_page_sharing_icons": "true",
					"player_width":500
					}
				}';
		
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                             curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_request);                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                           		'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); 
				$result = curl_exec($ch);
				curl_close($ch);
				$response_array = json_decode($result,true);
				if(!isset($response_array['error']) && !isset($response_array['meta']))
				{
					die("hiiii");
				}
				else
				{
					die("hjjjj");
				}
	
	
	}
	
	
	
	public function add(){
	
		// check user channel exists start//
		
		$this->loadModel('Channel');
		$channel_exists = $this->Channel->find('count',array('conditions'=>array('Channel.user_id'=>$this->Session->read('Auth.User.id'))));
		if($channel_exists == '0')
		{
			$this->Session->setFlash('Please create the channel first.', 'flash_bad');
			$this->redirect(array('controller'=>'channels','action'=>'channel_manager'));
		}
		
		// check user channel exists end //
	
		$this->layout = 'lay_dashboard';
		$this->set('title_for_layout','Add Stream');
		$time_zones = parent::tz_list();
		$this->set('time_zones',$time_zones);
		
		$broadcast_location = array_merge(Configure::read('Stream.Broadcast.Location'), Configure::read('4K.Broadcast.Location'));
		$this->set('broadcast_location',$broadcast_location);
		$aspect_ration_options =  array();
		$this->set('aspect_ration_options',$aspect_ration_options);
		$message  = '';
		$this->set('message',$message);
		
		
		
		
		if(!empty($this->request->data)){
			
			
			if (!isset($this->request->params['_Token']['key']) || ($this->request->params['_Token']['key'] != $this->request->params['_Token']['key'])) {
				$blackHoleCallback = $this->Security->blackHoleCallback;
				$this->$blackHoleCallback();
            }
			
			$this->Stream->set($this->request->data['Stream']);
			$this->Stream->setValidation('front_add');
			
			
			if ($this->Stream->validates()) {
			
				$aspect_ratio_array = explode('x',$this->request->data['Stream']['aspect_ratio']);
				
				$encoder_type = $this->request->data['Stream']['stream_encoder_type'];	
				if($this->request->data['Stream']['stream_encoder_type'] == 'ffsplit')
				{
					$encoder_type = 'other_rtmp';
				}
				
				
				$recording_enabled = "false";
				if($this->request->data['Stream']['recording_enabled'] == '1')
				{
					$recording_enabled = "true";
				}
				
				
				$add_stream_request = '{
				  "live_stream": {
					"name": "'.$this->request->data['Stream']['title'].'",
					"transcoder_type": "transcoded",
					"billing_mode": "pay_as_you_go",
					"broadcast_location": "'.$this->request->data['Stream']['stream_broadcast_location'].'",
					"recording":"'.$recording_enabled.'",
					"encoder": "'.$encoder_type.'",
					"delivery_method": "push",
					"disable_authentication":"true",
					"aspect_ratio_width": '.$aspect_ratio_array[0].',
					"aspect_ratio_height": '.$aspect_ratio_array[1].',
					"player_responsive": "true",
					"player_countdown": "false",
					"hosted_page": "true",
					"hosted_page_sharing_icons": "true",
					"player_width":500
					}
				}';
				
			
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/');
				//$ch = curl_init('https://api-sandbox.cloud.wowza.com/api/v1/live_streams/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                             curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_request);   
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                           		'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); // live
				
				/* curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json ;charset=utf-8', 
				'wsc-api-key:9lJxxxxxQQ5hv7ze2vr70hXNQTcoikFqXJ5nTcz5Q6Hn834j5MRH5G7YGjRcepIQ13146',
				'wsc-access-key:bKJgrvQ68b2ze51ARiQqcvsv8BQ9qp7hqYaqvcUim2JvNzJr4rl9yK0hvTcJ3562'
				)                                                                       
				);  *///sandbox
				
				
				
				$result = curl_exec($ch);
				curl_close($ch);
				$response_array = json_decode($result,true);
				/* echo $result;
				pr($response_array);
				die; */
				if(!isset($response_array['error']) && !isset($response_array['meta']))
				{
					$this->loadModel('Channel');
					$user_id = $this->Session->read('Auth.User.id');	
					$channel_detail = $this->Channel->find('first',array('conditions'=>array('Channel.user_id'=>$user_id),'fields'=>array('Channel.id','Channel.user_id')));
					$this->request->data['Stream']['stream_request'] = $add_stream_request;
					$this->request->data['Stream']['stream_response'] = $result;
					$this->request->data['Stream']['stream_key'] = $response_array['live_stream']['id'];
					$this->request->data['Stream']['player_id'] = $response_array['live_stream']['player_id'];
					if(isset($response_array['live_stream']['connection_code']))
					{
						$this->request->data['Stream']['connection_code'] = $response_array['live_stream']['connection_code'];
					}
					/* $this->request->data['Stream']['connection_code_expires_at'] = date('Y-m-d H:i:s',strtotime($response_array['live_stream']['connection_code_expires_at'])); */
					$this->request->data['Stream']['user_id'] = $user_id;
					if(isset($channel_detail) &&  !empty($channel_detail['Channel']['id']))
					{
						$this->request->data['Stream']['channel_id'] = $channel_detail['Channel']['id'];
					}
					$this->request->data['Stream']['stream_name'] = $response_array['live_stream']['source_connection_information']['stream_name'];
					$this->request->data['Stream']['primary_server'] = $response_array['live_stream']['source_connection_information']['primary_server'];
					if($this->Stream->save($this->request->data))
					{
					
						$stream_id	=	$this->Stream->id;
						// add scheduling start //
						
						if(isset($response_array['live_stream']['id']) && !empty($response_array['live_stream']['id']) && !empty($this->request->data['Stream']['schedule_start_date']) && !empty($this->request->data['Stream']['schedule_end_date']))
						{
						
							$schedule_start_date = $this->request->data['Stream']['schedule_start_date'].":00";
							$schedule_end_date = $this->request->data['Stream']['schedule_end_date'].":00";
							
							
							
							//$add_schedule_request = '{"schedule": {"transcoder_id": "'.$response_array['live_stream']['id'].'", "action_type": "start", "start_transcoder": "'.$schedule_start_date.'", "stop_transcoder": "'.$schedule_end_date.'", "recurrence_type": "once"}}';
							$add_schedule_request = '{"schedule": {"transcoder_id": "'.$response_array['live_stream']['id'].'", "action_type": "start", "start_transcoder": "'.$schedule_start_date.'", "stop_transcoder": "'.$schedule_end_date.'", "recurrence_type": "once"}}';
							$ch = curl_init('https://api.cloud.wowza.com/api/v1/schedules');
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");       
							curl_setopt($ch, CURLOPT_POSTFIELDS, $add_schedule_request);   
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
							curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
							'Content-Type: application/json ;charset=utf-8', 
								'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
								'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
								)                                                                       
							); // live
							
						
							$result_schedule = curl_exec($ch);
							curl_close($ch);
							$response_array_schedule = json_decode($result_schedule,true);
							
							
							if(!isset($response_array_schedule['error']))
							{
								$this->Stream->id	=	$stream_id;
								$this->Stream->saveField('schedule_id',$response_array_schedule['schedule']['id'],false);
							}
						}
						// add scheduling end //
						$p_image	=	$this->request->data['Stream']['image'];
					
						if (!empty($p_image) && $p_image['tmp_name'] != '' && $p_image['size'] > 0) {
				
							
							$allowed	=	array('jpg','jpeg','png');
							$temp 		= 	explode(".", $p_image["name"]);
							$extension 	= 	end($temp);
							$imageName 	= 	'stream_image_'.microtime(true).'.'.$extension;
							$files		=	$p_image;
							
							$result 	= 	$this->Upload->upload($files, WWW_ROOT . STREAM_IMAGE_FULL_DIR . DS, $imageName,'',$allowed);
							
							if(!empty($this->Upload->result) && empty($this->Upload->errors)) {
								$this->Stream->id	=	$stream_id;
								$this->Stream->saveField('stream_image',$imageName,false);
							}					
						}
					}
					
					
					$this->Session->setFlash("Streams add successfully.", 'flash_good');
					$this->redirect(array('controller'=>'streams','action'=>'index'));	
					
				}
				else
				{
					if(isset($response_array['error']))
					{
					$this->Session->setFlash($response_array['error'], 'flash_bad');
					}
					if(isset($response_array['meta']['message']))
					{
					$this->Session->setFlash($response_array['meta']['message'], 'flash_bad');
					
					}
					$this->redirect(array('controller'=>'streams','action'=>'index'));
					
				}	
		
			} else {
				
				
				if(!empty($this->request->data['Stream']['stream_broadcast_location']))
				{
				
				
					if (array_key_exists($this->request->data['Stream']['stream_broadcast_location'], Configure::read('Stream.Broadcast.Location')))
					{
						$aspect_ration_options =  array('1920x1080'=>'1920 x 1080(1080p)','1280x720'=>'1280 x 720(720p)','1024x576'=>'1024 x 576','896x504'=>'896 x 504','854x480'=>'854 x 480 (480p)','768x432'=>'768 x 432','640x360'=>'640 x 360','512x288'=>'512 x 288','384x216'=>'384 x 216','320x180'=>'320 x 180','256x144'=>'256 x 144','128x72'=>'128 x 72','768x576'=>'768 x 576 (PAL)','704x528'=>'704 x 528','640x480'=>'640 x 480','576x432'=>'576 x 432','512x384'=>'512 x 384','448x336'=>'448 x 336','384x288'=>'384 x 288','320x240'=>'320 x 240','256x192'=>'256 x 192','192x144'=>'192 x 144','128x96'=>'128 x 96','64x48'=>'64 x 48');
					
					
						$this->set('aspect_ration_options',$aspect_ration_options);
						
					}
					else if (array_key_exists($this->request->data['Stream']['stream_broadcast_location'], Configure::read('4K.Broadcast.Location')))
					{
						$aspect_ration_options =  array('3840x2160'=>'3840 x 2160','1920x1080'=>'1920 x 1080(1080p)','1280x720'=>'1280 x 720(720p)','1024x576'=>'1024 x 576','896x504'=>'896 x 504','854x480'=>'854 x 480 (480p)','768x432'=>'768 x 432','640x360'=>'640 x 360','512x288'=>'512 x 288','384x216'=>'384 x 216','320x180'=>'320 x 180','256x144'=>'256 x 144','128x72'=>'128 x 72','768x576'=>'768 x 576 (PAL)','704x528'=>'704 x 528','640x480'=>'640 x 480','576x432'=>'576 x 432','512x384'=>'512 x 384','448x336'=>'448 x 336','384x288'=>'384 x 288','320x240'=>'320 x 240','256x192'=>'256 x 192','192x144'=>'192 x 144','128x96'=>'128 x 96','64x48'=>'64 x 48');
						
						$this->set('aspect_ration_options',$aspect_ration_options);
					}
					
					
					
		
					
					
					if($this->request->data['Stream']['aspect_ratio']=='3840x2160')
					{
						$message = 'This setting creates <strong>7 bitrate renditions.</strong>';
					}
					else if($this->request->data['Stream']['aspect_ratio']=='1920x1080')
					{
						$message = 'This setting creates <strong>6 bitrate renditions.</strong>';
					}
					else if($this->request->data['Stream']['aspect_ratio']=='1280x720')
					{
						$message = 'This setting creates <strong>5 bitrate renditions.</strong>';
					}
					else if($this->request->data['Stream']['aspect_ratio']=='1024x576')
					{
						$message = 'This setting creates <strong>5 bitrate renditions.</strong>';
					}
					$this->set('message',$message);
					
				}
				
				$this->Session->setFlash("Record has not been created", 'flash_bad');
			}
		
		}
		$streaming_guide_pdf = Configure::read("STREAMING_GUIDE_PDF");
		$this->set('streaming_guide_pdf',$streaming_guide_pdf);
	
	
	}
	
	
	
	public function edit($id){
		
		//$this->set('id',$id);
		$this->layout = 'lay_dashboard';
		$this->set('title_for_layout','Edit Stream');
		$stream_data = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.title','Stream.schedule_id','Stream.stream_key')));
		$this->set('stream_data',$stream_data);
		
		
		
		if(!empty($this->request->data)){
			
			if (!isset($this->request->params['_Token']['key']) || ($this->request->params['_Token']['key'] != $this->request->params['_Token']['key'])) {
				$blackHoleCallback = $this->Security->blackHoleCallback;
				$this->$blackHoleCallback();
            }
			
			$this->Stream->set($this->request->data['Stream']);
			$this->Stream->setValidation('front_edit');
			
			
			if ($this->Stream->validates()) {
			
				$stream_data =  $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.stream_key')));
				$edit_stream_request = '{"live_stream": {"name": "'.$this->request->data['Stream']['title'].'"}}';
				
				/* echo 'https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_data['Stream']['stream_key'].'/';
				die;
				 */
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_data['Stream']['stream_key'].'/');
				//$ch = curl_init('https://api-sandbox.cloud.wowza.com/api/v1/live_streams/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");  
				curl_setopt($ch, CURLOPT_POSTFIELDS, $edit_stream_request);   
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
					'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); // live
				
				/* curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json ;charset=utf-8', 
				'wsc-api-key:9lJxxxxxQQ5hv7ze2vr70hXNQTcoikFqXJ5nTcz5Q6Hn834j5MRH5G7YGjRcepIQ13146',
				'wsc-access-key:bKJgrvQ68b2ze51ARiQqcvsv8BQ9qp7hqYaqvcUim2JvNzJr4rl9yK0hvTcJ3562'
				)                                                                       
				);  *///sandbox
				
				
				
				$result = curl_exec($ch);
				curl_close($ch);
				$response_array = json_decode($result,true);
				
				
				if(!isset($response_array['error']) && !isset($response_array['meta']))
				{
					
					$this->loadModel('Channel');
					$user_id = $this->Session->read('Auth.User.id');	
					$channel_detail = $this->Channel->find('first',array('conditions'=>array('Channel.user_id'=>$user_id),'fields'=>array('Channel.id','Channel.user_id')));
					
					$this->request->data['Stream']['stream_request'] = $edit_stream_request;
					$this->request->data['Stream']['stream_response'] = $result;
					$this->request->data['Stream']['stream_key'] = $response_array['live_stream']['id'];
					$this->request->data['Stream']['player_id'] = $response_array['live_stream']['player_id'];
					
					if(isset($response_array['live_stream']['connection_code']))
					{
						$this->request->data['Stream']['connection_code'] = $response_array['live_stream']['connection_code'];
					}
					
					/* $this->request->data['Stream']['connection_code_expires_at'] = date('Y-m-d H:i:s',strtotime($response_array['live_stream']['connection_code_expires_at'])); */
					
					
					//$this->request->data['Stream']['user_id'] = $user_id; 
					 
					/* $this->request->data['Stream']['channel_id'] = $channel_detail['Channel']['id']; */
					$this->request->data['Stream']['stream_name'] = $response_array['live_stream']['source_connection_information']['stream_name'];
					$this->request->data['Stream']['primary_server'] = $response_array['live_stream']['source_connection_information']['primary_server'];
					
					
					
					$this->Stream->id = $id;
					if($this->Stream->save($this->request->data))
					{
						$stream_id	=	$id;
						
						// edit scheduling start //
						if(!empty($stream_data['Stream']['schedule_id']))
						{
						
							
							
							$edit_schedule_request = '{"schedule": {"transcoder_id": "'.$stream_data['Stream']['stream_key'].'","name": "'.$this->request->data['Stream']['title'].'"}}';
							$ch = curl_init('https://api.cloud.wowza.com/api/v1/schedules');
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");       
							curl_setopt($ch, CURLOPT_POSTFIELDS, $edit_schedule_request);   
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
							curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
							'Content-Type: application/json ;charset=utf-8', 
								'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
								'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
								)                                                                       
							); // edit
							
							
							$result_schedule = curl_exec($ch);
							curl_close($ch);
							$response_array_schedule = json_decode($result_schedule,true);
							
							
							if(!isset($response_array_schedule['error']))
							{
								$this->Stream->id	=	$stream_id;
								$this->Stream->saveField('schedule_id',$response_array_schedule['schedule']['id'],false);
							}
						}
						
						// edit scheduling end //
						
						
						
						$p_image	=	$this->request->data['Stream']['image'];
						$old_image	=	$this->request->data['Stream']['stream_image'];
						
					
						if (!empty($p_image) && $p_image['tmp_name'] != '' && $p_image['size'] > 0) {
				
							$allowed	=	array('jpg','jpeg','png');
							$temp 		= 	explode(".", $p_image["name"]);
							$extension 	= 	end($temp);
							$imageName 	= 	'stream_image_'.microtime(true).'.'.$extension;
							$files		=	$p_image;
							
							$result 	= 	$this->Upload->upload($files, WWW_ROOT . STREAM_IMAGE_FULL_DIR . DS, $imageName,'',$allowed);
							
							if(!empty($this->Upload->result) && empty($this->Upload->errors)) {
								
								if($old_image &&  file_exists(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$old_image )) {
									@unlink(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$old_image);
								}
								
								$this->Stream->id	=	$stream_id;
								$this->Stream->saveField('stream_image',$imageName,false);
							}
							
						}
					}
					$this->Session->setFlash("Streams updated successfully.", 'flash_good');
					$this->redirect(array('controller'=>'streams','action'=>'index'));	
					
				}
				else
				{
					$this->Session->setFlash($response_array['error'], 'flash_bad');
					
				}
			} else {
				
				$this->Session->setFlash("Record has not been updated", 'flash_bad');
			}
		}
		else
		{
			$this->request->data = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id)));
		}
	}
	
	
	
	public function index(){
		if (!isset($this->params['named']['page'])) {
            $this->Session->delete('FrontSearch');
        }
		
		$filters	=	array();
        if (!empty($this->request->data)) {
			$this->Session->delete('AdminSearch');
			if (isset($this->request->data['Stream']['title']) && $this->request->data['Stream']['title'] != '') {
				$title = trim($this->request->data['Stream']['title']);
				$this->Session->write('FrontSearch.title', $title);
				
			}
		}
		
		if ($this->Session->check('FrontSearch')) {
            $keywords 	= 	$this->Session->read('FrontSearch');
			foreach($keywords as $key=>$values){
				if($key == 'title'){
					$filters[] = array('Stream.'.$key.' LIKE'=>"%".$values."%");					
				}
				$this->admin_exportcsv($this->request->data);
			}
		}
		
		
		
		$user_id = $this->Session->read('Auth.User.id'); 
		$filters[] =  array('Stream.user_id'=>$user_id);
		$this->paginate = array('Stream' => array(
			'limit' =>Configure::read('App.PageLimit'),
			'order' => array('Stream.id' => 'DESC'),
			'conditions' => $filters,
        ));
		$data = $this->paginate('Stream');	
			
		$this->set(compact('data'));
		$this->set('title_for_layout', __('Stream Listing', true));

		$this->layout = 'lay_dashboard';
		
	
	
	}
	
	/* public function detail($id){
	
		
		$this->layout = 'dashboard';
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.player_id')));
		$this->set('stream_detail',$stream_detail);
		
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/players/'.$stream_detail['Stream']['player_id'].'/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(   
		'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$response_array = json_decode($result,true);
		//if(isset($response_array['meta']['status']) && $response_array['meta']['status'] == '404')
		if(isset($response_array['error']) && isset($response_array['meta']))
		{
			$response_array = array();
		}
		
		$this->set('response_array',$response_array);
		
		
	} */
	
	
	 public function add_schedule(){
		
	
		/* $ch = curl_init('https://api.cloud.wowza.com/api/v1/transcoders/fnl7ljvm/');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
		'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); // live
	
		$result = curl_exec($ch);
		curl_close($ch);
		$response_array = json_decode($result,true);
		pr($response_array); */
		
		
		
		
		
		
		$add_schedule = '{"schedule": {"transcoder_id": "pbvvgrjf", "action_type": "start", "start_transcoder": "2016-08-25 11:15:00","stop_transcoder":"2016-08-26 11:15:00", "recurrence_type": "once"}}';
		
		
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/schedules');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");       
		curl_setopt($ch, CURLOPT_POSTFIELDS, $add_schedule);   
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
		'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); // live
		
	
		$result = curl_exec($ch);
		echo $result."hiiiiii";
		curl_close($ch);
		
		$response_array = json_decode($result,true);
		pr($response_array);
		die("");
		 
		
	}
	
	
	public function add_sandbox_stream()
	{
		$this->layout = "lay_stream_detail";
		$this->set('title_for_layout','Add Stream');
		$player_logo = SITE_URL.'img/Front/logo.png';
		$add_stream_json_string = '{
		  "live_stream": {
			"name": "My Live Stream",
			"transcoder_type": "transcoded",
			"billing_mode": "pay_as_you_go",
			"broadcast_location": "eu_germany",
			"recording": true,
			"closed_caption_type": "none",
			"encoder": "wowza_gocoder",
			"delivery_method": "push",
			"delivery_type": "single-bitrate",
			"delivery_protocol": "hls-hds",
			"use_stream_source": false,
			"aspect_ratio_width": 1920,
			"aspect_ratio_height": 1080,
			"player_logo_image":"'.$player_logo.'",
			"remove_player_logo_image": "false",
			"player_logo_position": "top-right"
		  }
		}';
		
		$ch = curl_init('https://api-sandbox.cloud.wowza.com/api/v1/live_streams/');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_json_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:9lJxxxxxQQ5hv7ze2vr70hXNQTcoikFqXJ5nTcz5Q6Hn834j5MRH5G7YGjRcepIQ13146',
			'wsc-access-key:bKJgrvQ68b2ze51ARiQqcvsv8BQ9qp7hqYaqvcUim2JvNzJr4rl9yK0hvTcJ3562'
			)                                                                       
		);                                                                                                                   
																															 
		$result = curl_exec($ch);
		curl_close($ch);
		echo $result;
		die("hiiii");
		
		
		$response = '{
	"live_stream": {
		"id": "29g9s54w",
		"name": "My Live Stream",
		"transcoder_type": "transcoded",
		"billing_mode": "pay_as_you_go",
		"broadcast_location": "eu_germany",
		"recording": true,
		"closed_caption_type": "none",
		"encoder": "wowza_gocoder",
		"delivery_method": "push",
		"delivery_protocol": "hls-hds",
		"use_stream_source": false,
		"aspect_ratio_width": 1920,
		"aspect_ratio_height": 1080,
		"connection_code": "020531",
		"connection_code_expires_at": "2016-07-12T10:36:55.000Z",
		"source_connection_information": {
			"primary_server": "78a4e8.entrypoint.cloud.wowza.com",
			"host_port": 1935,
			"application": "app-9c70",
			"stream_name": "cceecfa9",
			"disable_authentication": false,
			"username": "client11469",
			"password": "edf107a4"
		},
		"video_fallback": false,
		"player_id": "ypgybrff",
		"player_responsive": false,
		"player_width": 640,
		"player_countdown": false,
		"player_embed_code": "in_progress",
		"player_hds_playback_url": "http://wowzaprodhd68-lh.akamaihd.net/z/20aeccce_1@42948/manifest.f4m",
		"player_hls_playback_url": "http://wowzaprodhd68-lh.akamaihd.net/i/20aeccce_1@42948/master.m3u8",
		"hosted_page": true,
		"hosted_page_title": "My Live Stream",
		"hosted_page_url": "in_progress",
		"hosted_page_sharing_icons": true,
		"stream_targets": [{
			"id": "4n9z7m1v"
		}],
		"created_at": "2016-07-11T10:36:55.000Z",
		"updated_at": "2016-07-11T10:36:56.000Z",
		"links": [{
			"rel": "self",
			"method": "GET",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w"
		}, {
			"rel": "update",
			"method": "PATCH",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w"
		}, {
			"rel": "state",
			"method": "GET",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/state"
		}, {
			"rel": "thumbnail_url",
			"method": "GET",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/thumbnail_url"
		}, {
			"rel": "start",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/start"
		}, {
			"rel": "reset",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/reset"
		}, {
			"rel": "stop",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/stop"
		}, {
			"rel": "regenerate_connection_code",
			"method": "PUT",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w/regenerate_connection_code"
		}, {
			"rel": "delete",
			"method": "DELETE",
			"href": "https://api.cloud.wowza.com/api/v1/live_streams/29g9s54w"
		}]
	}
	}';
		die("hiiiiii");
		
	}
	
	
	
	public function start($id)
	{
		$this->layout = false;
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.stream_key')));
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_detail['Stream']['stream_key'].'/start/');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_request);    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                           		'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$response_array = json_decode($result,true);
		
		if(!isset($response_array['error']))
		{
			
			if($response_array['live_stream']['state'] == 'starting')
			{
				
				$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
				$this->request->data['Stream']['state'] = 1;
				//$this->request->data['Stream']['connection_code'] = NULL;
				//$this->request->data['Stream']['connection_code_expires_at'] = NULL;
				$this->Stream->save($this->request->data);
				$this->Session->setFlash("Stream started successfully. This may take a few minutes. Thank you for your patience.", 'flash_good');	
				$this->redirect(array('controller'=>'streams','action'=>'detail',$id));
			}
			else
			{
				$this->Session->setFlash($response_array['error'], 'admin_flash_bad');
				$this->redirect(array('controller'=>'streams','action'=>'detail',$id));
			}
			
		}
		else
		{
			$this->Session->setFlash($response_array['error'], 'admin_flash_bad');
			$this->redirect(array('controller'=>'streams','action'=>'detail',$id));
		}
	}
	
	
	public function stop($id)
	{
		$this->layout = false;
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.stream_key')));
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_detail['Stream']['stream_key'].'/stop/');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_request);    
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                           		'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$response_array = json_decode($result,true);
		if(!isset($response_array['error']))
		{
			if($response_array['live_stream']['state'] == 'stopped')
			{
				$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
				$this->request->data['Stream']['state'] = 2;
				//$this->request->data['Stream']['connection_code'] = NULL;
				$this->Stream->save($this->request->data);
				$this->Session->setFlash("Stream stopped successfully. This may take a few minutes. Thank you for your patience.", 'flash_good');
				$this->redirect(array('controller'=>'streams','action'=>'detail',$id));						
			}
			else
			{
				$this->Session->setFlash($response_array['error'], 'admin_flash_bad');
				$this->redirect(array('controller'=>'streams','action'=>'detail',$id));
			}
			
		}
		else
		{
			$this->Session->setFlash($response_array['error'], 'admin_flash_bad');
			$this->redirect(array('controller'=>'streams','action'=>'detail',$id));
		}
	}
	
	
	
	public function get_connection_code($stream_id)
	{
		$this->layout = false;
		$this->autoRender = false;
		
		if(!empty($stream_id))
		{
			
			
			$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$stream_id),'fields'=>array('Stream.id','Stream.stream_key')));
			$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_detail['Stream']['stream_key'].'/regenerate_connection_code/');
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_request);    
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(         
				'Content-Type: application/json ;charset=utf-8', 
				'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
				'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
				)                                                                       
			); 
			$result = curl_exec($ch);
			curl_close($ch);
			$response_array = json_decode($result,true);
			
			
			if(!isset($response_array['error']))
			{
				$connection_code = $response_array['live_stream']['connection_code'];
				if(!empty($connection_code))
				{
					
					$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
					$this->request->data['Stream']['connection_code'] = $connection_code;
					if($this->Stream->save($this->request->data))
					{
						$response = array('key'=>'success','connection_code'=>$connection_code);
						echo json_encode($response);
						die;
					}
					/* $this->Session->setFlash("Connection code getting successfully.", 'flash_good');
					$this->redirect($this->referer());	 */					
				}
				else
				{
					$this->Session->setFlash("Connection code not getting successfully.Please tray again later", 'admin_flash_bad');
					$response = array('key'=>'failed','connection_code'=>'');
					echo json_encode($response);
					die;
					
					/* $this->redirect($this->referer()); */
				}
				
			}
			else
			{
				$this->Session->setFlash("Connection code not getting successfully.Please tray again later", 'admin_flash_bad');
				$response = array('key'=>'failed','connection_code'=>'');
				echo json_encode($response);
				die;
				/* $this->Session->setFlash($response_array['error'], 'admin_flash_bad');
				$this->redirect($this->referer()); */
			} 
		}
		
	}
	
	
	
	/* public function get_recordings()
	{
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/recordings/cfcb2h6b/state/');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                           		
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		 $result = curl_exec($ch);
		
		curl_close($ch);
		$response_array = json_decode($result,true);
		pr($response_array);
		die;
	} */
	
	
	
	public function channel_manager()
	{	
		$this->loadModel('User');
		$id = $this->Auth->User('id');
		$user_detail = $this->User->find('first', array('conditions' => array('User.id' => $id)));
		$this->set('user_detail',$user_detail);
		$this->layout = 'lay_dashboard';
		$this->set('title_for_layout','Channel Manager');
	}


	public function settings()
	{	
		$this->layout = 'lay_dashboard';
		$this->set('title_for_layout','Streaming  Settings');
	}		
	
	
	public function detail($id)
	{
		$this->layout = 'lay_dashboard';
		$this->set('title_for_layout','Stream Detail');
		
		
		
		
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.title','Stream.subject','Stream.stream_bio','Stream.stream_state','Stream.player_id','Stream.primary_server','Stream.stream_key','Stream.stream_name','Stream.aspect_ratio','Stream.stream_image','Stream.schedule_start_date','Stream.schedule_end_date','Stream.connection_code','Stream.recording_enabled','Stream.stream_encoder_type')));
		
		$this->set('stream_detail',$stream_detail);
	}
	
	
	public function start_stream($id)
	{
	
		// check stream start //
		$this->layout = false;
		$this->autoRender = false;
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.stream_key')));
		
		/* $ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_detail['Stream']['stream_key'].'/state/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch); */
		$response_array = $this->get_stream_status($stream_detail['Stream']['stream_key']);
		
		if(!isset($response_array['error']))
		{
			if($response_array['live_stream']['state'] == 'started' || $response_array['live_stream']['state'] == 'starting')
			{
				$this->Session->setFlash("Streams already running.", 'flash_good');	
				$response = array('key'=>'success_already_running','msg'=>'Stream started successfully. This may take a few minutes. Thank you for your patience.');
				echo json_encode($response);
				die;
			}
		}
		
		// check stream end  //
		
		//$response = array('key'=>'success','msg'=>'Streams start successfully.This may take a few minutes. Thank you for your patience.');
		//$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.stream_key')));
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_detail['Stream']['stream_key'].'/start/');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$response_array = json_decode($result,true);
		
		if(!isset($response_array['error']))
		{
			if($response_array['live_stream']['state'] == 'starting')
			{
				$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
				//$this->request->data['Stream']['state'] = 1;
				$this->request->data['Stream']['state'] = STARTING;
				if($this->Stream->save($this->request->data));
				{
					$response = array('key'=>'success','msg'=>'Stream started successfully. This may take a few minutes. Thank you for your patience.');
					echo json_encode($response);
					die;
				}
			}
			else
			{
				echo $response = array('key'=>'failed','msg'=>$response_array['error']);
				die;
				
			}
			
		}
		else
		{
			echo $response = array('key'=>'failed','msg'=>$response_array['error']);
			die;
		}
	}
	
	
	public function check_stream_status($id)
	{
		$this->layout = false;
		$this->autoRender = false;
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.stream_key')));
	/* 	
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_detail['Stream']['stream_key'].'/state/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$response_array = json_decode($result,true); */
		$response_array = $this->get_stream_status($stream_detail['Stream']['stream_key']);
		if(!isset($response_array['error']))
		{
			$stream_state = 0;
			if($response_array['live_stream']['state'] == 'starting')
			{
				$stream_state = 1;
			}
			elseif($response_array['live_stream']['state'] == 'started')
			{
				$stream_state = 2;
				$this->Session->setFlash("Streams start successfully.Thank you for your patience.", 'flash_good');	
			}
			elseif($response_array['live_stream']['state'] == 'stopping')
			{
				$stream_state = 3;
			}
			elseif($response_array['live_stream']['state'] == 'stopped')
			{
				$stream_state = 4;
			}
			elseif($response_array['live_stream']['state'] == 'resetting')
			{
				$stream_state = 5;
			}
			$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
			$this->request->data['Stream']['stream_state'] = $stream_state;
			if($this->Stream->save($this->request->data));
			{
				
				$response = array('key'=>'success','stream_state'=>$stream_state);
				echo json_encode($response);
				die;
			}
		}
		else
		{
			echo $response = array('key'=>'failed');
			die;
		} 
	}	
	
	
	public function stop_stream($id)
	{
		$this->layout = false;
		$this->autoRender = false;
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.stream_key')));
		
		$response_array_result = $this->get_stream_status($stream_detail['Stream']['stream_key']);
		
		if(!isset($response_array_result['error']))
		{
			if($response_array_result['live_stream']['state'] == 'started')
			{
			
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_detail['Stream']['stream_key'].'/stop/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
				//curl_setopt($ch, CURLOPT_POSTFIELDS, $add_stream_request);    
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(           
					'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); 
				$result = curl_exec($ch);
				curl_close($ch);
				$response_array = json_decode($result,true);
				if(!isset($response_array['error']))
				{
					if($response_array['live_stream']['state'] == 'stopped')
					{
						$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
						//$this->request->data['Stream']['stream_state'] = 4;
						$this->request->data['Stream']['stream_state'] = STOPPED;
						//$this->request->data['Stream']['connection_code'] = NULL;
						$this->Stream->save($this->request->data);
						$this->Session->setFlash("Stream stopped successfully. This may take a few minutes. Thank you for your patience.", 'flash_good');
						$this->redirect(array('controller'=>'streams','action'=>'detail',$id));					
					}
					else
					{
						$this->Session->setFlash($response_array['error'], 'admin_flash_bad');
						$this->redirect(array('controller'=>'streams','action'=>'detail',$id));
					}
					
				}
				else
				{
					$this->Session->setFlash($response_array['error'], 'admin_flash_bad');
					$this->redirect(array('controller'=>'streams','action'=>'detail',$id));
				}
			}
			else if($response_array['live_stream']['state'] == 'stopped')
			{
				$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
				//$this->request->data['Stream']['stream_state'] = 4;
				$this->request->data['Stream']['stream_state'] = STOPPED;
				$this->Stream->save($this->request->data);
				$this->Session->setFlash("Stream stopped successfully. This may take a few minutes. Thank you for your patience.", 'flash_good');
				$this->redirect(array('controller'=>'streams','action'=>'detail',$id));		
			
			}
			else
			{
				$this->request->data['Stream']['id'] = $stream_detail['Stream']['id'];
				//$this->request->data['Stream']['stream_state'] = 4;
				$this->request->data['Stream']['stream_state'] = STOPPED;
				$this->Stream->save($this->request->data);
				$this->Session->setFlash("Stream stopped successfully. This may take a few minutes. Thank you for your patience.", 'flash_good');
				$this->redirect(array('controller'=>'streams','action'=>'detail',$id));	
			}
		}
	}
	
	
	public function get_all_recordings()
	{
		
		
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel('RecordingStream');
		$get_all_streams = $this->Stream->find('all',array('fields'=>array('Stream.id','Stream.stream_key','Stream.user_id','Stream.stream_bio','Stream.channel_id')));
		$recordings_data_array = array();
		if(!empty($get_all_streams))
		{
			foreach($get_all_streams as $key=>$value)
			{
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/transcoders/'.$value['Stream']['stream_key'].'/recordings/');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
				'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); 
				$result = curl_exec($ch);
				curl_close($ch);
				$response_array = json_decode($result,true);
				
				if(!empty($response_array['recordings']))
				{
					foreach($response_array['recordings'] as $k=>$v)
					{ 
						if($v['state'] == 'completed')
						{
							/* $thumb = $value['Stream']['user_id'].'_'.$value['Stream']['stream_key'].'_'.$v['id'].'.jpg';
							$thumbnail = '/var/www/html/app/webroot/uploads/recording_images/'.$thumb;
							$time = '00:00:10';
							exec("/usr/bin/ffmpeg -i ".$v['download_url']." -an -ss " . $time . " -an -r 1 -s qcif -vframes 1 -filter:v scale=280:160 -y $thumbnail 2>&1");
							$check_record_existing = $this->RecordingStream->find('count',array('conditions'=>array('RecordingStream.user_id'=>$value['Stream']['user_id'],'RecordingStream.recording_key'=>$v['id'],'RecordingStream.stream_key'=>$value['Stream']['stream_key']))); */
							
							
							$check_record_existing = $this->RecordingStream->find('count',array('conditions'=>array('RecordingStream.recording_key'=>$v['id'],'RecordingStream.stream_key'=>$value['Stream']['stream_key'])));
							
							if($check_record_existing == 0 && $v['file_size'] >0 && $v['duration'] > 0)
							{
								
								$thumb = $value['Stream']['user_id'].'_'.$value['Stream']['stream_key'].'_'.$v['id'].'.jpg';
								$thumbnail = '/var/www/html/app/webroot/uploads/recording_images/'.$thumb;
								$time = '00:00:10';
								exec("/usr/bin/ffmpeg -i ".$v['download_url']." -an -ss " . $time . " -an -r 1 -s qcif -vframes 1 -filter:v scale=280:160 -y $thumbnail 2>&1");
								
								
								$recordings_data['RecordingStream']['description'] = $value['Stream']['stream_bio'];
								$recordings_data['RecordingStream']['user_id'] = $value['Stream']['user_id'];
								$recordings_data['RecordingStream']['stream_key'] = $value['Stream']['stream_key'];
								$recordings_data['RecordingStream']['channel_id'] = $value['Stream']['channel_id'];
								$recordings_data['RecordingStream']['stream_id'] = $value['Stream']['id'];
								$recordings_data['RecordingStream']['image'] = $thumb;
								
								$recordings_data['RecordingStream']['title'] = $v['transcoder_name'];
								$recordings_data['RecordingStream']['recording_key'] = $v['id'];
								//$recordings_data['RecordingStream']['file_name'] = $v['file_name'];
								$recordings_data['RecordingStream']['file_size'] = $v['file_size'];
								$recordings_data['RecordingStream']['duration'] = $v['duration'];
								$recordings_data['RecordingStream']['download_url'] = $v['download_url'];
								$recordings_data['RecordingStream']['created'] = date('Y-m-d H:i:s',strtotime($v['created_at']));
								$recordings_data['RecordingStream']['modified'] = date('Y-m-d H:i:s',strtotime($v['updated_at']));;
								$recordings_data_array[] = $recordings_data;
								$recordings_data =array();
							}
						}
					}
				}
			}
		}
		if(!empty($recordings_data_array))
		{
			$this->RecordingStream->saveAll($recordings_data_array);
		}
		die;
		
	}
	
		
	
	public function recorded_stream_detail($id)
	{	
		$this->layout = 'lay_stream_detail';
		$this->loadModel('RecordingStream');
		$this->loadModel('ChannelFollower');
		$this->loadModel('Message');
	
		$user_id = $this->Auth->User('id');
		$user_detail = $this->User->find('first', array('conditions' => array('User.id' => $user_id),'fields'=>array('User.profile_image','User.nickname')));
		
		$this->Message->bindModel(array(
			'belongsTo'=>array(
				'User'=>array(
					'className'=>'User',
					'foreignKey'=>'sender_id',
					'fields'=>array('User.profile_image','User.nickname')
				)
			)
		),false);
		
		$this->RecordingStream->bindModel(array(
                'hasOne' => array(
                    'ChannelFollower' => array(
                        'className' => 'ChannelFollower',
                        'foreignKey' => 'recording_stream_id',
                        'fields' => array('is_follow'), 
						'conditions'=>array('ChannelFollower.user_id'=>$this->Auth->user('id'),'ChannelFollower.recording_stream_id !='=>'0'),
                    ),
                ),
				'hasMany'=>array(
					'Message'=>array(
						'order'=>array('Message.created'=>'Asc')
					)
				),'belongsTo'=>array(
					'Channel','User'
				)		
			), false
		);
		$recorded_stream_detail = $this->RecordingStream->find('first',array('recursive'=>2,'conditions'=>array('RecordingStream.id'=>$id)));
		$total_unique_viewers = $this->get_stream_unique_viewers_count($recorded_stream_detail['RecordingStream']['stream_key']);
		$this->set('total_unique_viewers',$total_unique_viewers);

		 
		$related_recorded_stream_listing = $this->RecordingStream->find('all',array('conditions'=>array('RecordingStream.user_id'=>$recorded_stream_detail['RecordingStream']['user_id'],'RecordingStream.id != '=>$id)));
		
		$streamCountData	=	$this->ChannelFollower->find('count',array('conditions'=>array('ChannelFollower.recording_stream_id'=>$id,'ChannelFollower.is_follow'=>1)));
		
		
		
		
		$this->set('streamCountData',$streamCountData);
		$this->set('user_detail',$user_detail);
		$this->set('recorded_stream_detail',$recorded_stream_detail);
		$this->set('title_for_layout',$recorded_stream_detail['RecordingStream']['title']);
		$this->set('related_recorded_stream_listing',$related_recorded_stream_listing);
	}
	
	
	
	
	public function stream_aspect_ration_options()
	{
		$this->layout = false;
		$this->autoRender = false;
		$aspect_ration_options =  array();
		if(!empty($_POST['broadcast_location']))
		{
			if (array_key_exists($_POST['broadcast_location'], Configure::read('Stream.Broadcast.Location')))
			{
				$aspect_ration_options =  array('1920x1080'=>'1920 x 1080(1080p)','1280x720'=>'1280 x 720(720p)','1024x576'=>'1024 x 576','896x504'=>'896 x 504','854x480'=>'854 x 480 (480p)','768x432'=>'768 x 432','640x360'=>'640 x 360','512x288'=>'512 x 288','384x216'=>'384 x 216','320x180'=>'320 x 180','256x144'=>'256 x 144','128x72'=>'128 x 72','768x576'=>'768 x 576 (PAL)','704x528'=>'704 x 528','640x480'=>'640 x 480','576x432'=>'576 x 432','512x384'=>'512 x 384','448x336'=>'448 x 336','384x288'=>'384 x 288','320x240'=>'320 x 240','256x192'=>'256 x 192','192x144'=>'192 x 144','128x96'=>'128 x 96','64x48'=>'64 x 48');
				
			}
			else if (array_key_exists($_POST['broadcast_location'], Configure::read('4K.Broadcast.Location')))
			{
				$aspect_ration_options =  array('3840x2160'=>'3840 x 2160','1920x1080'=>'1920 x 1080(1080p)','1280x720'=>'1280 x 720(720p)','1024x576'=>'1024 x 576','896x504'=>'896 x 504','854x480'=>'854 x 480 (480p)','768x432'=>'768 x 432','640x360'=>'640 x 360','512x288'=>'512 x 288','384x216'=>'384 x 216','320x180'=>'320 x 180','256x144'=>'256 x 144','128x72'=>'128 x 72','768x576'=>'768 x 576 (PAL)','704x528'=>'704 x 528','640x480'=>'640 x 480','576x432'=>'576 x 432','512x384'=>'512 x 384','448x336'=>'448 x 336','384x288'=>'384 x 288','320x240'=>'320 x 240','256x192'=>'256 x 192','192x144'=>'192 x 144','128x96'=>'128 x 96','64x48'=>'64 x 48');
			}
		}
		$this->set('aspect_ration_options',$aspect_ration_options);
		$this->render('/Elements/Front/Streams/stream_aspect_ration_options');
		
	}
	
	
	
	public function  bitrate_renditions()
	{
		$this->layout = false;
		$this->autoRender = false;
		$message = '';
		if(!empty($_POST['aspect_ratio']))
		{
			if($_POST['aspect_ratio']=='3840x2160')
			{
				$message = 'This setting creates <strong>7 bitrate renditions.</strong>';
			}
			else if($_POST['aspect_ratio']=='1920x1080')
			{
				$message = 'This setting creates <strong>6 bitrate renditions.</strong>';
			}
			else if($_POST['aspect_ratio']=='1280x720')
			{
				$message = 'This setting creates <strong>5 bitrate renditions.</strong>';
			}
			else if($_POST['aspect_ratio']=='1024x576')
			{
				$message = 'This setting creates <strong>5 bitrate renditions.</strong>';
			}
			
		}
		echo $message;
		die;;
	}
	
	
	
	public function get_stream_status($stream_key)
	{
		/* $stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.id','Stream.stream_key')));
		 */
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_key.'/state/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
			'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); 
		$result = curl_exec($ch);
		curl_close($ch);
		$response_array = array();
		$response_array = json_decode($result,true);
		return $response_array;
	}
	
	
	public function upcoming_streams_listing()
	{
		$this->layout = "front";
		$this->set('title_for_layout','Upcoming Streams');
		$this->paginate = array('Stream' => array(
			'limit' =>Configure::read('App.PageLimit'),
			'order' => array('Stream.title' => 'DESC'),
			'conditions' => array('Stream.schedule_start_date >' => date('Y-m-d H:i:s'),'Stream.status'=>Configure::read('App.Status.active'))
		));
		$upcoming_streams_listing = $this->paginate('Stream');
		$this->set('upcoming_streams_listing',$upcoming_streams_listing);
	}
	
	
	public function delete($id)
	{
		
		$this->layout = false;
		$this->autoRender = false;
		$this->loadModel('RecordingStream');
		$this->loadModel('Message');
		$this->loadModel('ChannelFollower');
		$this->loadModel('ChannelSubscription');
		$get_stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id)));
		if(!empty($get_stream_detail))
		{
			$RecordingStream = $this->RecordingStream->find('all',array('conditions'=>array('RecordingStream.stream_id'=>$id)));
			if(!empty($RecordingStream))
			{
				foreach($RecordingStream as $recording_key=>$recording_value)
				{
				
					if($recording_value['RecordingStream']['image'] &&  file_exists(WWW_ROOT.RECORDING_IMAGE_FULL_DIR.DS.$recording_value['RecordingStream']['image'] )) 
					{
						@unlink(WWW_ROOT.RECORDING_IMAGE_FULL_DIR.DS.$recording_value['RecordingStream']['image']);
					}
					
					$ch = curl_init('https://api.cloud.wowza.com/api/v1/recordings/'.$recording_value['RecordingStream']['recording_key'].'/');
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
					'Content-Type: application/json ;charset=utf-8', 
						'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
						'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
						)                                                                       
					); 
					$result = curl_exec($ch);
					curl_close($ch);
					
				}
				$this->RecordingStream->deleteAll(array('RecordingStream.user_id' => $id));
				
			}
		
			if($get_stream_detail['Stream']['stream_image'] &&  file_exists(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$get_stream_detail['Stream']['stream_image'] )) 
			{
				@unlink(WWW_ROOT.STREAM_IMAGE_FULL_DIR.DS.$get_stream_detail['Stream']['stream_image']);
			}
			
			if(!empty($get_stream_detail['Stream']['stream_key']))
			{
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$get_stream_detail['Stream']['stream_key'].'/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
				'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); 
				$result = curl_exec($ch);
				curl_close($ch);
			}
			if(!empty($get_stream_detail['Stream']['schedule_id']))
			{
				$ch = curl_init('https://api.cloud.wowza.com/api/v1/schedules/'.$get_stream_detail['Stream']['schedule_id'].'/');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(    
				'Content-Type: application/json ;charset=utf-8', 
					'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
					'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
					)                                                                       
				); 
				$result = curl_exec($ch);
				curl_close($ch);
			}
			$this->Stream->deleteAll(array('Stream.id' => $id));
			$this->Message->deleteAll(array('Message.stream_id' => $id));
			$this->ChannelFollower->deleteAll(array('ChannelFollower.stream_id' => $id));
			$this->ChannelSubscription->deleteAll(array('ChannelSubscription.stream_id' => $id));
			$this->Session->setFlash("Streams delete successfully.", 'flash_good');
			$this->redirect(array('controller'=>'streams','action'=>'index'));	
			
		}
		else
		{
			$this->Session->setFlash("Stream not deleted successfully.Please try again later.", 'flash_bad');
			$this->redirect(array('controller'=>'streams','action'=>'index'));	
		}
	}
	
	
	public function update_stream_unique_viewers($id)
	{
		$this->layout = false;
		$this->autoRender = false;
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id),'fields'=>array('Stream.stream_key')));
		$total_unique_viewers = 0;
		if(!empty($stream_detail) && !empty($stream_detail['Stream']['stream_key']))
		{
			$total_unique_viewers = $this->get_stream_unique_viewers_count($stream_detail['Stream']['stream_key']);
		
		} 
		$response = array('key'=>'success','total_unique_viewers'=>$total_unique_viewers);
		echo json_encode($response);
		die;
	
	}
	
	
	public function get_stream_unique_viewers_count($stream_key)
	{
		$total_unique_viewers = 0;
		$ch = curl_init('https://api.cloud.wowza.com/api/v1/live_streams/'.$stream_key.'/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(     
		'Content-Type: application/json ;charset=utf-8', 
			'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
			'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
			)                                                                       
		); // live
		
		$result = curl_exec($ch);
		curl_close($ch);
		$stream_detail_response_array = json_decode($result,true);
		if(!isset($stream_detail_response_array['error']) && !isset($stream_detail_response_array['meta']))
		{
			if(isset($stream_detail_response_array['live_stream']['stream_targets']) && !empty($stream_detail_response_array['live_stream']['stream_targets']))
			{
				foreach($stream_detail_response_array['live_stream']['stream_targets'] as $key=>$value)
				{
					
					$current_time = date('Y-m-d H:i:s');
					$five_minute_ago_time = date('Y-m-d H:i:s',(time() - 300));
					$ch = curl_init('https://api.cloud.wowza.com/api/v1/usage/viewer_data/stream_targets/'.$value['id'].'/');
					$get_stream_viewers = '{"from":"'.$current_time.'","to":"'.$five_minute_ago_time.'"}'; 
					/* curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");     
					curl_setopt($ch, CURLOPT_POSTFIELDS, $get_stream_viewers);   */   
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
						'Content-Type: application/json ;charset=utf-8', 
						'wsc-api-key:'.Configure::read('WOWZA_API_KEY'),
						'wsc-access-key:'.Configure::read('WOWZA_ACCESS_KEY')
						)                                                                       
					); 
					$result = curl_exec($ch);
					curl_close($ch);
					$stream_viewers_response_array = json_decode($result,true); 
					$total_unique_viewers =  $stream_viewers_response_array['stream_target']['total_unique_viewers'];
				}
			}
		} 
		
		return $total_unique_viewers;
	}
	
	
	public function admin_ajax_stream_sorting() { 
        if ($this->request->data['sort'] && is_array($this->request->data['sort'])) {
			$this->loadModel('Stream');
            foreach ($this->request->data['sort'] as $key => $value) {
              	
				$this->request->data['Stream']['id'] = $value;
				$this->request->data['Stream']['sorting'] = $key;			
				$this->Stream->save($this->request->data);		
				
            }
        }
        die(json_encode(array("status" => "success")));
    }
	
	
	public function check_user_stream_running_status_count()
	{
		$this->layout = false;
		$this->autoRender = false;
		$check_stream_running_status_count = 0;
		if(!empty($_POST))
		{
		
			$check_stream_running_status_count = $this->Stream->find('count',array('conditions'=>array('Stream.id != '=>$_POST['stream_id'],'Stream.user_id'=>$this->Session->read('Auth.User.id'),'Stream.stream_state'=>STARTED)));
		}
		$response = array('check_stream_running_status_count'=>$check_stream_running_status_count);
		echo json_encode($response);
		die;
	}
	
	
	
	public function update_schedule_stream_staus_cron()
	{
		$this->layout = false;
		$this->autoRender = false;
		$conditions = array();
	/* 	$conditions[] = array('Stream.schedule_start_date IS NOT NULL','Stream.schedule_end_date IS NOT NULL');
		$get_schedule_stream = $this->Stream->find('all',array('conditions'=>$conditions,'fields'=>array('Stream.stream_key','Stream.id','Stream.stream_state')));
		 */
		$get_schedule_stream = $this->Stream->find('all',array('fields'=>array('Stream.stream_key','Stream.id','Stream.stream_state')));
		/* pr($get_schedule_stream);
		die; */
		
		if(!empty($get_schedule_stream))
		{
			foreach($get_schedule_stream as $key=>$value)
			{
			
				$response_array_result = $this->get_stream_status($value['Stream']['stream_key']);
				$response_array_result['live_stream']['state'];
				if(!isset($response_array_result['error']))
				{
					if($response_array_result['live_stream']['state'] == 'starting' && $value['Stream']['stream_state'] != STARTING)
					{
						$this->Stream->id	=	$value['Stream']['id'];
						$this->Stream->saveField('stream_state',STARTING,false);
					}
					if($response_array_result['live_stream']['state'] == 'started' && $value['Stream']['stream_state'] != STARTED)
					{
						$this->Stream->id	=	$value['Stream']['id'];
						$this->Stream->saveField('stream_state',STARTED,false);
					}
					if($response_array_result['live_stream']['state'] == 'stopping' && $value['Stream']['stream_state'] != STOPPING)
					{
						$this->Stream->id	=	$value['Stream']['id'];
						$this->Stream->saveField('stream_state',STOPPING,false);
					}
					if($response_array_result['live_stream']['state'] == 'stopped' && $value['Stream']['stream_state'] != STOPPED)
					{
						$this->Stream->id	=	$value['Stream']['id'];
						$this->Stream->saveField('stream_state',STOPPED,false);
					}
				} 
			}
		}
		die;
	}
	
	
	
	public function donate_for_stream($id)
	{
		
		/* $this->User->bindModel(array(
			'hasOne'=>array(
				'UserDetail'=>array(
					'className'=>'UserDetail',
					'foreignKey'=>'user_id',
					'fields'=>array('id','user_id','paypal_email')	
				)
			)			
		),false);
		
		
		$this->Channel->bindModel(array(
			'belongsTo'=>array(
				'User'=>array(
					'className'=>'User',
					'foreignKey'=>'user_id',
					'fields'=>array('id','first_name','profile_image')	
				)
			)
		),false);
		$this->set('channelData',$channelData); */
		
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id)));
		$this->set('stream_detail',$stream_detail);
		
	}
	
	
	
	public function paypal_notify_donate_stream()
	{
		$this->layout = false;
	// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
		// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
		// Set this to 0 once you go live or don't require logging.
		define("DEBUG", 1);
		// Set to 0 once you're ready to go live
		define("USE_SANDBOX", 1);
		define("LOG_FILE", "./ipn.log");
		// Read POST data
		// reading posted data directly from $_POST causes serialization
		// issues with array data in POST. Reading raw POST data from input stream instead.
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}
		// Post IPN data back to PayPal to validate the IPN data is genuine
		// Without this step anyone can fake IPN data
		if(USE_SANDBOX == true) {
			$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}
		$ch = curl_init($paypal_url);
		if ($ch == FALSE) {
			return FALSE;
		}
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		if(DEBUG == true) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		}
		// CONFIG: Optional proxy configuration
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		// Set TCP timeout to 30 seconds
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
		// of the certificate as shown below. Ensure the file is readable by the webserver.
		// This is mandatory for some environments.
		//$cert = __DIR__ . "./cacert.pem";
		//curl_setopt($ch, CURLOPT_CAINFO, $cert);
		$res = curl_exec($ch);
		if (curl_errno($ch) != 0) // cURL error
			{
			if(DEBUG == true) {	
				/* error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE); */
			}
			curl_close($ch);
			exit;
		} else {
				// Log the entire HTTP response if debug is switched on.
				if(DEBUG == true) {
					/* error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
					error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE); */
				}
				curl_close($ch);
		}
		// Inspect IPN validation result and act accordingly
		// Split response headers and payload, a better way for strcmp
		$tokens = explode("\r\n\r\n", trim($res));
		$res = trim(end($tokens));
		if (strcmp ($res, "VERIFIED") == 0) {
			
			if(!empty($_POST)){
			
				$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream_id'=>$_POST['item_number'])));
				$this->loadModel('ChannelTransaction');
				$check_existing_transaction_entry = $this->ChannelTransaction->find('count',array('conditions'=>array('ChannelTransaction.txn_id'=>$_POST['txn_id'])));
				if(!empty($_POST['payment_status']) && $_POST['payment_status']=='Completed' && $check_existing_transaction_entry == '0'){
				
					$transactionData['ChannelTransaction']['user_id'] = $_POST['custom'];
					$transactionData['ChannelTransaction']['channel_id'] = $stream_detail['Stream']['channel_id'];
					$transactionData['ChannelTransaction']['stream_id'] = $_POST['item_number'];
					$transactionData['ChannelTransaction']['txn_id'] = $_POST['txn_id'];
					$transactionData['ChannelTransaction']['price'] = $_POST['mc_gross'];
					$transactionData['ChannelTransaction']['created'] = date('Y-m-d H:i:s');
					$this->ChannelTransaction->save($transactionData);
					die;
				}
			}
			if(DEBUG == true) {
				error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
			}
		
		}else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			// Add business logic here which deals with invalid IPN messages
			if(DEBUG == true) {
				error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
			}
		}
	}	
	
	
	public function paypal_cancel_donate_stream($id)
	{
		$this->layout = false;
		$this->Session->setFlash('Your transaction is cancel.', 'flash_popup_error');
		
		$this->redirect(array('controller'=>'streams','action'=>'stream_detail',$id));
	
		
	}


	public function paypal_return_donate_stream($id)
	{
		$this->layout = false;
		/* 
		$_POST['payment_status'] = 'Completed';
		$_POST['custom'] = 120;
		$_POST['item_number'] = 9;
		$_POST['txn_id'] = '444193457A7191314';
		$_POST['mc_gross'] = '1.00'; */
		if(!empty($_POST)){
			/* $this->loadModel('ChannelTransaction');
			$check_existing_transaction_entry = $this->ChannelTransaction->find('count',array('conditions'=>array('ChannelTransaction.txn_id'=>$_POST['txn_id']))); */
			//if(!empty($_POST['payment_status']) && $_POST['payment_status']=='Completed' && $check_existing_transaction_entry == '0'){
			if($_POST['payment_status']=='Completed'){
			
				/* $transactionData['ChannelTransaction']['user_id'] = $_POST['custom'];
				$transactionData['ChannelTransaction']['channel_id'] = $_POST['item_number'];
				$transactionData['ChannelTransaction']['txn_id'] = $_POST['txn_id'];
				$transactionData['ChannelTransaction']['price'] = $_POST['mc_gross'];
				$transactionData['ChannelTransaction']['created'] = date('Y-m-d H:i:s');
				$this->ChannelTransaction->save($transactionData); */
				$this->Session->setFlash('Your transaction is successful.', 'flash_success');
				$this->redirect(array('controller'=>'streams','action'=>'stream_detail',$id));
			
			}
			else
			{
				$this->Session->setFlash('Your transaction is failed.Please try again later.', 'flash_popup_error');
				$this->redirect(array('controller'=>'streams','action'=>'stream_detail',$id));
			}	
		}
		else
		{
			$this->Session->setFlash('Your transaction is failed.Please try again later.', 'flash_popup_error');
			$this->redirect(array('controller'=>'channels','action'=>'channel_detail',$id));
		}	
		
		
	}
	
	
	
	public function donate_for_recorded_stream($id)
	{
		
		/* $this->User->bindModel(array(
			'hasOne'=>array(
				'UserDetail'=>array(
					'className'=>'UserDetail',
					'foreignKey'=>'user_id',
					'fields'=>array('id','user_id','paypal_email')	
				)
			)			
		),false);
		
		
		$this->Channel->bindModel(array(
			'belongsTo'=>array(
				'User'=>array(
					'className'=>'User',
					'foreignKey'=>'user_id',
					'fields'=>array('id','first_name','profile_image')	
				)
			)
		),false);
		$this->set('channelData',$channelData); */
		
		$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream.id'=>$id)));
		$this->set('stream_detail',$stream_detail);
		
	}
	
	
	
	public function paypal_notify_donate_recorded_video()
	{
		$this->layout = false;
	// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
		// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
		// Set this to 0 once you go live or don't require logging.
		define("DEBUG", 1);
		// Set to 0 once you're ready to go live
		define("USE_SANDBOX", 1);
		define("LOG_FILE", "./ipn.log");
		// Read POST data
		// reading posted data directly from $_POST causes serialization
		// issues with array data in POST. Reading raw POST data from input stream instead.
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}
		// Post IPN data back to PayPal to validate the IPN data is genuine
		// Without this step anyone can fake IPN data
		if(USE_SANDBOX == true) {
			$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}
		$ch = curl_init($paypal_url);
		if ($ch == FALSE) {
			return FALSE;
		}
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		if(DEBUG == true) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		}
		// CONFIG: Optional proxy configuration
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		// Set TCP timeout to 30 seconds
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
		// of the certificate as shown below. Ensure the file is readable by the webserver.
		// This is mandatory for some environments.
		//$cert = __DIR__ . "./cacert.pem";
		//curl_setopt($ch, CURLOPT_CAINFO, $cert);
		$res = curl_exec($ch);
		if (curl_errno($ch) != 0) // cURL error
			{
			if(DEBUG == true) {	
				/* error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE); */
			}
			curl_close($ch);
			exit;
		} else {
				// Log the entire HTTP response if debug is switched on.
				if(DEBUG == true) {
					/* error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
					error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE); */
				}
				curl_close($ch);
		}
		// Inspect IPN validation result and act accordingly
		// Split response headers and payload, a better way for strcmp
		$tokens = explode("\r\n\r\n", trim($res));
		$res = trim(end($tokens));
		if (strcmp ($res, "VERIFIED") == 0) {
			
			if(!empty($_POST)){
			
				$stream_detail = $this->Stream->find('first',array('conditions'=>array('Stream_id'=>$_POST['item_number'])));
				$this->loadModel('ChannelTransaction');
				$check_existing_transaction_entry = $this->ChannelTransaction->find('count',array('conditions'=>array('ChannelTransaction.txn_id'=>$_POST['txn_id'])));
				if(!empty($_POST['payment_status']) && $_POST['payment_status']=='Completed' && $check_existing_transaction_entry == '0'){
				
					$transactionData['ChannelTransaction']['user_id'] = $_POST['custom'];
					$transactionData['ChannelTransaction']['channel_id'] = $stream_detail['Stream']['channel_id'];
					$transactionData['ChannelTransaction']['stream_id'] = $_POST['item_number'];
					$transactionData['ChannelTransaction']['txn_id'] = $_POST['txn_id'];
					$transactionData['ChannelTransaction']['price'] = $_POST['mc_gross'];
					$transactionData['ChannelTransaction']['created'] = date('Y-m-d H:i:s');
					$this->ChannelTransaction->save($transactionData);
					die;
				}
			}
			if(DEBUG == true) {
				error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
			}
		
		}else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			// Add business logic here which deals with invalid IPN messages
			if(DEBUG == true) {
				error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
			}
		}
	}	
	
	
	public function paypal_cancel_donate_recorded_video($id)
	{
		$this->layout = false;
		$this->Session->setFlash('Your transaction is cancel.', 'flash_popup_error');
		
		$this->redirect(array('controller'=>'streams','action'=>'recorded_stream_detail',$id));
	
		
	}


	public function paypal_return_donate_recorded_video($id)
	{
		$this->layout = false;
		/* 
		$_POST['payment_status'] = 'Completed';
		$_POST['custom'] = 120;
		$_POST['item_number'] = 9;
		$_POST['txn_id'] = '444193457A7191314';
		$_POST['mc_gross'] = '1.00'; */
		if(!empty($_POST)){
			/* $this->loadModel('ChannelTransaction');
			$check_existing_transaction_entry = $this->ChannelTransaction->find('count',array('conditions'=>array('ChannelTransaction.txn_id'=>$_POST['txn_id']))); */
			//if(!empty($_POST['payment_status']) && $_POST['payment_status']=='Completed' && $check_existing_transaction_entry == '0'){
			if($_POST['payment_status']=='Completed'){
			
				/* $transactionData['ChannelTransaction']['user_id'] = $_POST['custom'];
				$transactionData['ChannelTransaction']['channel_id'] = $_POST['item_number'];
				$transactionData['ChannelTransaction']['txn_id'] = $_POST['txn_id'];
				$transactionData['ChannelTransaction']['price'] = $_POST['mc_gross'];
				$transactionData['ChannelTransaction']['created'] = date('Y-m-d H:i:s');
				$this->ChannelTransaction->save($transactionData); */
				$this->Session->setFlash('Your transaction is successful.', 'flash_success');
				$this->redirect(array('controller'=>'streams','action'=>'recorded_stream_detail',$id));
			
			}
			else
			{
				$this->Session->setFlash('Your transaction is failed.Please try again later.', 'flash_popup_error');
				$this->redirect(array('controller'=>'streams','action'=>'recorded_stream_detail',$id));
			}	
		}
		else
		{
			$this->Session->setFlash('Your transaction is failed.Please try again later.', 'flash_popup_error');
			$this->redirect(array('controller'=>'channels','action'=>'channel_detail',$id));
		}	
		
		
	}
	
	
	
	
	
	
	
	
	
	
}
