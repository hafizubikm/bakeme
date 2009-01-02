<?php
class User extends AppModel {

	var $name = 'User';
	var $validate = array(
		'group_id' => array(array('rule' => 'numeric')),
		'firstname' => array(array('rule' => array('minlength', 1))),
		'email' => array(array('rule' => 'email'))
	);

	function beforeSave(){
		if(!empty($this->data['User']['new_password'])){
			$this->data['User']['password'] = Security::hash($this->data['User']['new_password'], null, true);
		}
		if(!empty($this->data['User']['your_password'])){
			$this->data['User']['password'] = Security::hash($this->data['User']['your_password'], null, true);
		}
		return parent::beforeSave();
	}

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Group' => array('className' => 'Group',
								'foreignKey' => 'group_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
	);

}
?>