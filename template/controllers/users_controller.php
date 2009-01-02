<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form');

	function admin_login(){
		if(!empty($this->user)){
			$this->redirect('/admin/');
		}
	}

	function admin_logout(){
		$this->Auth->logout();
		$this->redirect('/');
	}

	function admin_index() {
	}

	function admin_paging() {
		$this->User->recursive = 0;
		$filters = $this->User->getFilters($this->passedArgs);
		$this->set('users', $this->paginate('User', $filters));
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid User.', true), 'default', array(), 'error');
			$this->redirect(array('action'=>'index'));
		}
		$user = $this->User->read(null, $id);
		$this->set('user', $user);
	}

	function admin_form($form_action = 'add', $id = null) {
		if(!empty($id)){
			$user = $this->User->read(null, $id);
			$this->set('user', $user);
		}
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved', true), 'default', array(), 'info');
				if(!empty($id)){
					$this->redirect(array('action' => 'view', $id));
				}else{
					$this->redirect($this->referer());
				}
			} else {
				$this->Session->setFlash(__('The User could not be saved. Please, try again.', true), 'default', array(), 'error');
			}
		}else{
			$this->data = array();
			$this->data['User'] = array();
			if(!empty($user)){
				$this->data = $user;
			}
			foreach($this->passedArgs as $fieldName => $value){
				$this->data['User'][$fieldName] = $value;
			}
		}
		$groups = $this->User->Group->generateTreeList(null, "{n}.Group.id", "{n}.Group.name", '--', 0);
		$this->set(compact('groups'));
		$form_url = '/' . $this->params['url']['url'];
		$this->set('form_url', $form_url);
		$this->set('form_action', $form_action);
	}

	function admin_add() {
	}

	function admin_edit($id = null) {
		$this->set('id', $id);
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for User', true), 'default', array(), 'error');
		} else {
			$this->User->del($id);
			$this->Session->setFlash(__('User deleted', true), 'default', array(), 'info');
		}
		$this->redirect($this->referer(array('action'=>'index')));
	}

	function admin_undelete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for User', true), 'default', array(), 'error');
		} else {
			$this->User->undelete($id);
			$this->Session->setFlash(__('User restored', true), 'default', array(), 'info');
		}
		$this->redirect($this->referer(array('action'=>'index')));
	}

	function admin_hard_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for User', true), 'default', array(), 'error');
		} else {
			$this->User->hardDelete($id);
			$this->Session->setFlash(__('User deleted', true), 'default', array(), 'info');
		}
		$this->redirect($this->referer(array('action'=>'index')));
	}

}
?>