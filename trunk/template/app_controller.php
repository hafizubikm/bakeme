<?php
class AppController extends Controller {
	var $view = "Theme";
 	var $components = array('Auth', 'RequestHandler', 'Email');
 	var $helpers = array("Html", "Form", "ExtendedForm", "Menu", "Javascript", "Ajax");
 	
 	var $paginate = array('limit' => 10);
 	
 	var $path;
 	var $user;
 	var $menuItems;
 	
	function beforeFilter() {
		header('Content-type: text/html; charset="utf-8"');
		uses('L10n');
		//backend is protected
		if(!empty($this->params['admin']) && $this->params['admin'] == 1){
			$this->layout = "admin";
			if (isset($this->Auth)) {
				$this->Auth->userModel = 'User';
				$this->Auth->fields = array('username' => 'email', 'password' => 'password');
	    		$this->Auth->loginAction = '/admin/users/login';
	    		$this->Auth->loginRedirect = '/admin/users/';
			   	$this->Auth->autoRedirect = true;
			   	$this->user = $this->Auth->user();
			   	if(empty($this->user) && $this->RequestHandler->isAjax()){
			   		$this->render(null, 'ajax', APP . 'views/elements/ajax_login_message.ctp');
			   	}
			   	$this->set('user', $this->user);
			   	$this->_setAdminMenu();
	  		}
	  		if($this->name == 'Users' && $this->action == 'admin_login'){
	  			$this->layout = 'admin_login';
	  		}
		}else{
			$this->Auth->allow();
		}
	}
	
	function _setAdminMenu(){
		$this->menuItems = array (
			'items' => array ()
			);
		//general
		$this->menuItems['items']['General'] = array('target' => '/admin', 'items' => array());
		$this->menuItems['items']['General']['items']['Users'] = array('target' => '/admin/users', 'items' => array());
		//logout
		$this->menuItems['items']['Logout'] = array (
			'target' => '/admin/users/logout',
			'items' => array ()
		);
		$this->set('menuItems', $this->menuItems);
	}

}
?>