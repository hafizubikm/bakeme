<?php
/* SVN FILE: $Id:controller.php 6574 2008-03-15 05:45:43Z gwoo $ */
/**
 * The ControllerTask handles creating and updating controller files.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008,	Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.console.libs.tasks
 * @since			CakePHP(tm) v 1.2
 * @version			$Revision:6574 $
 * @modifiedby		$LastChangedBy:gwoo $
 * @lastmodified	$Date:2008-03-15 06:45:43 +0100 (za, 15 mrt 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Task class for creating and updating controller files.
 *
 * @package		cake
 * @subpackage	cake.cake.console.libs.tasks
 */
class ControllerTask extends Shell {
/**
 * Name of plugin
 *
 * @var string
 * @access public
 */
	var $plugin = null;
/**
 * Tasks to be loaded by this Task
 *
 * @var array
 * @access public
 */
	var $tasks = array('Project');
/**
 * path to CONTROLLERS directory
 *
 * @var array
 * @access public
 */
	var $path = CONTROLLERS;
/**
 * Override initialize
 *
 * @access public
 */
	function initialize() {
	}
/**
 * Execution method always used for tasks
 *
 * @access public
 */
	function execute() {
		if (empty($this->args)) {
			$this->__interactive();
		}

		if (isset($this->args[0])) {
			if($this->args[0] == 'all')
			{
				$useDbConfig = 'default';
				$controllers = $this->listAll($useDbConfig, 'Controllers');
				$admin = $this->getAdmin();
				foreach($controllers as $controllerName)
				{
					$actions = null;
					$controllerName = Inflector::camelize($controllerName);
					$currentModelName = $this->_modelName($controllerName);
					if (App::import('Model', $currentModelName)) {
						if (isset($this->args[1]) && $this->args[1] == 'admin')
						{
							$actions = $this->bakeActions($controllerName, $admin);
							$this->bake($controllerName, $actions);
						}
					}
				}
			}
			else
			{
				$controller = Inflector::camelize($this->args[0]);
				$actions = null;
				if (isset($this->args[1]) && $this->args[1] == 'scaffold') {
					$this->out('Baking scaffold for ' . $controller);
					$actions = $this->bakeActions($controller);
				} else {
					$actions = 'scaffold';
				}
				if ((isset($this->args[1]) && $this->args[1] == 'admin') || (isset($this->args[2]) && $this->args[2] == 'admin')) {
					if ($admin = $this->getAdmin()) {
						$this->out('Adding ' . Configure::read('Routing.admin') .' methods');
						if ($actions == 'scaffold') {
							$actions = $this->bakeActions($controller, $admin);
						} else {
							$actions .= $this->bakeActions($controller, $admin);
						}
					}
				}
				if ($this->bake($controller, $actions)) {
					/*
					if ($this->_checkUnitTest()) {
						$this->bakeTest($controller);
					}
					*/
				}
			}
		}
	}
/**
 * Interactive
 *
 * @access private
 */
	function __interactive($controllerName = false) {
		if (!$controllerName) {
			$this->interactive = true;
			$this->hr();
			$this->out(sprintf("Bake Controller\nPath: %s", $this->path));
			$this->hr();
			$actions = '';
			$uses = array();
			$helpers = array();
			$components = array();
			$wannaUseSession = 'y';
			$wannaDoAdmin = 'n';
			$wannaUseScaffold = 'n';
			$wannaDoScaffolding = 'y';
			$controllerName = $this->getName();
		}
		$this->hr();
		$this->out("Baking {$controllerName}Controller");
		$this->hr();

		$controllerFile = low(Inflector::underscore($controllerName));

		$question[] = __("Would you like to build your controller interactively?", true);
		if (file_exists($this->path . $controllerFile .'_controller.php')) {
			$question[] = sprintf(__("Warning: Choosing no will overwrite the %sController.", true), $controllerName);
		}
		$doItInteractive = $this->in(join("\n", $question), array('y','n'), 'n');

		if (low($doItInteractive) == 'y' || low($doItInteractive) == 'yes') {
			$this->interactive = true;

			$wannaUseScaffold = $this->in(__("Would you like to use scaffolding?", true), array('y','n'), 'n');

			if (low($wannaUseScaffold) == 'n' || low($wannaUseScaffold) == 'no') {

				$wannaDoScaffolding = $this->in(__("Would you like to include some basic class methods (index(), add(), view(), edit())?", true), array('y','n'), 'y');

				if (low($wannaDoScaffolding) == 'y' || low($wannaDoScaffolding) == 'yes') {
					$wannaDoAdmin = $this->in(__("Would you like to create the methods for admin routing?", true), array('y','n'), 'y');
				}

				$wannaDoHelpers = $this->in(__("Would you like this controller to use other helpers besides HtmlHelper and FormHelper?", true), array('y','n'), 'n');

				if (low($wannaDoHelpers) == 'y' || low($wannaDoHelpers) == 'yes') {
					$helpersList = $this->in(__("Please provide a comma separated list of the other helper names you'd like to use.\nExample: 'Ajax, Javascript, Time'", true));
					$helpersListTrimmed = str_replace(' ', '', $helpersList);
					$helpers = explode(',', $helpersListTrimmed);
				}
				$wannaDoComponents = $this->in(__("Would you like this controller to use any components?", true), array('y','n'), 'n');

				if (low($wannaDoComponents) == 'y' || low($wannaDoComponents) == 'yes') {
					$componentsList = $this->in(__("Please provide a comma separated list of the component names you'd like to use.\nExample: 'Acl, Security, RequestHandler'", true));
					$componentsListTrimmed = str_replace(' ', '', $componentsList);
					$components = explode(',', $componentsListTrimmed);
				}

				$wannaUseSession = $this->in(__("Would you like to use Sessions?", true), array('y','n'), 'y');
			} else {
				$wannaDoScaffolding = 'n';
			}
		} else {
			$wannaDoScaffolding = $this->in(__("Would you like to include some basic class methods (index(), add(), view(), edit())?", true), array('y','n'), 'y');

			if (low($wannaDoScaffolding) == 'y' || low($wannaDoScaffolding) == 'yes') {
				$wannaDoAdmin = $this->in(__("Would you like to create the methods for admin routing?", true), array('y','n'), 'y');
			}
		}
		$admin = false;

		if ((low($wannaDoAdmin) == 'y' || low($wannaDoAdmin) == 'yes')) {
			$admin = $this->getAdmin();
		}

		if (low($wannaDoScaffolding) == 'y' || low($wannaDoScaffolding) == 'yes') {
			$actions = $this->bakeActions($controllerName, null, in_array(low($wannaUseSession), array('y', 'yes')));
			if ($admin) {
				$actions .= $this->bakeActions($controllerName, $admin, in_array(low($wannaUseSession), array('y', 'yes')));
			}
		}

		if ($this->interactive === true) {
			$this->out('');
			$this->hr();
			$this->out('The following controller will be created:');
			$this->hr();
			$this->out("Controller Name:  $controllerName");

			if (low($wannaUseScaffold) == 'y' || low($wannaUseScaffold) == 'yes') {
				$this->out("		   var \$scaffold;");
				$actions = 'scaffold';
			}

			if (count($helpers)) {
				$this->out("Helpers:      ", false);

				foreach ($helpers as $help) {
					if ($help != $helpers[count($helpers) - 1]) {
						$this->out(ucfirst($help) . ", ", false);
					} else {
						$this->out(ucfirst($help));
					}
				}
			}

			if (count($components)) {
				$this->out("Components:      ", false);

				foreach ($components as $comp) {
					if ($comp != $components[count($components) - 1]) {
						$this->out(ucfirst($comp) . ", ", false);
					} else {
						$this->out(ucfirst($comp));
					}
				}
			}
			$this->hr();
			$looksGood = $this->in(__('Look okay?', true), array('y','n'), 'y');

			if (low($looksGood) == 'y' || low($looksGood) == 'yes') {
				$baked = $this->bake($controllerName, $actions, $helpers, $components, $uses);
				/*
				if ($baked && $this->_checkUnitTest()) {
					$this->bakeTest($controllerName);
				}
				*/
			} else {
				$this->__interactive($controllerName);
			}
		} else {
			$baked = $this->bake($controllerName, $actions, $helpers, $components, $uses);
			/*
			if ($baked && $this->_checkUnitTest()) {
				$this->bakeTest($controllerName);
			}
			*/
		}
	}
/**
 * Bake scaffold actions
 *
 * @param string $controllerName Controller name
 * @param string $admin Admin route to use
 * @param boolean $wannaUseSession Set to true to use sessions, false otherwise
 * @return string Baked actions
 * @access private
 */
	function bakeActions($controllerName, $admin = null, $wannaUseSession = true) {
		$currentModelName = $this->_modelName($controllerName);
		if (!App::import('Model', $currentModelName)) {
			$this->err(__('You must have a model for this class to build scaffold methods. Please try again.', true));
			exit;
		}
		$actions = null;
		$modelObj =& new $currentModelName();
		$controllerPath = $this->_controllerPath($controllerName);
		$pluralName = $this->_pluralName($currentModelName);
		$singularName = Inflector::variable($currentModelName);
		$singularPath = Inflector::underscore(Inflector::singularize($currentModelName));
		$singularHumanName = Inflector::humanize($currentModelName);
		$pluralHumanName = Inflector::humanize($controllerName);
		//
		if(!empty($admin)){
			$admin_url = '/admin';
		}
		
		//check if this is the users controller: if so: add the login and logout methods
		if($pluralName == 'users')
		{
			$actions .= "\n";
			$actions .= "\tfunction admin_login(){\n";
			$actions .= "\t\tif(!empty(\$this->user)){\n";
			$actions .= "\t\t\t\$this->redirect('/admin/');\n";
			$actions .= "\t\t}\n";
			$actions .= "\t}\n";
			$actions .= "\n";
			$actions .= "\tfunction admin_logout(){\n";
			$actions .= "\t\t\$this->Auth->logout();\n";
			$actions .= "\t\t\$this->redirect('/');\n";
			$actions .= "\t}\n";
		}
		
		//
		$actions .= "\n";
		$actions .= "\tfunction {$admin}index() {\n";
		$actions .= "\t}\n";
		$actions .= "\n";
		$actions .= "\tfunction {$admin}paging() {\n";
		$actions .= "\t\t\$this->{$currentModelName}->recursive = 0;\n";
		$actions .= "\t\t\$filters = \$this->{$currentModelName}->getFilters(\$this->passedArgs);\n";
		$actions .= "\t\t\$this->set('{$pluralName}', \$this->paginate('{$currentModelName}', \$filters));\n";
		$actions .= "\t}\n";
		$actions .= "\n";
		if($modelObj->hasField('order')) {
			$actions .= "\tfunction admin_sort() {\n";
			$actions .= "\t\tif(!empty(\$this->params['form']) && !empty(\$this->params['form']['{$pluralName}ListOrder'])){\n";
			$actions .= "\t\t\tforeach (\$this->params['form']['{$pluralName}ListOrder'] as \$order => \$id){\n";
			$actions .= "\t\t\t\t\$data['{$currentModelName}']['order'] = \$order;\n";
			$actions .= "\t\t\t\t\$this->{$currentModelName}->id = \$id;\n";
			$actions .= "\t\t\t\t\$this->{$currentModelName}->saveField('order', \$order);\n";
			$actions .= "\t\t\t}\n";
			$actions .= "\t\t}\n";
			$actions .= "\t\t\$this->{$currentModelName}->recursive = 0;\n";
			$actions .= "\t\t\$filters = \$this->{$currentModelName}->getFilters(\$this->passedArgs);\n";
			$actions .= "\t\t\$this->set('{$pluralName}', \$this->{$currentModelName}->find('all', array('conditions' => \$filters, 'order' => '{$currentModelName}.order')));\n";
			$actions .= "\t}\n";
			$actions .= "\n";
		}
		
		/* VIEW ACTION */
		$actions .= "\tfunction {$admin}view(\$id = null) {\n";
		$actions .= "\t\tif (!\$id) {\n";
		$actions .= "\t\t\t\$this->Session->setFlash(__('Invalid {$singularHumanName}.', true), 'default', array(), 'error');\n";
		$actions .= "\t\t\t\$this->redirect(array('action'=>'index'));\n";
		$actions .= "\t\t}\n";
		$actions .= "\t\t\$$singularName = \$this->{$currentModelName}->read(null, \$id);\n";
		$actions .= "\t\t\$this->set('".$singularName."', \$$singularName);\n";
		//other models
		foreach($modelObj->hasAndBelongsToMany as $associationName => $relation) {
			if(!empty($associationName)) {
				$otherModelName = $this->_modelName($associationName);
				$otherSingularName = $this->_singularName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				$otherControllerPath = $this->_controllerPath($otherModelName);
				$actions .= "\t\t//\$this->_setRelatedInfo(\${$singularName}, '{$otherModelName}', \$this->{$currentModelName}->{$otherModelName});\n";
				$actions .= "\t\t\$this->set('{$otherPluralName}FormAction', '{$admin_url}/{$otherControllerPath}/add/{$singularPath}/'.\$id);\n";
				//bekijken, bewerken, wissen
				$actions .= "\t\t\${$otherPluralName}ListActions = array();\n";
				$actions .= "\t\t\${$otherPluralName}ListActions[] = array('label' => 'Verwijderen', 'confirmation' => 'Weet u zeker dat u de koppeling met dit item wil verwijderen?', 'ajax' => true, 'target' => '/admin/{$controllerPath}/delete_" . strtolower($otherModelName) . "/'.\$id.'/[id]');\n";
				$actions .= "\t\t\$this->set('{$otherPluralName}ListActions', \${$otherPluralName}ListActions);\n";
			}
		}
		foreach($modelObj->hasMany as $associationName => $relation) {
			if(!empty($associationName)) {
				$otherModelName = $this->_modelName($associationName);
				$otherSingularName = $this->_singularName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				$otherControllerPath = $this->_controllerPath($otherModelName);
				$otherModelKey = Inflector::underscore($otherModelName);
				$otherModelObj =& ClassRegistry::getObject($otherModelKey);
				$orderField = $otherModelName . '.' . $otherModelObj->primaryKey;
				if($otherModelObj->hasField('order')) $orderField = $otherModelName . '.order';
				$actions .= "\t\t\$this->set('{$otherPluralName}FormAction', '{$admin_url}/{$otherControllerPath}/add/{$singularPath}/'.\$id);\n";
				//associaties voor formulier
				foreach($otherModelObj->hasAndBelongsToMany as $otherAssociationName => $otherRelation) {
					if(!empty($otherAssociationName)) {
						$otherOtherModelName = $this->_modelName($otherAssociationName);
						if($otherOtherModelName != $currentModelName){
							$otherOtherSingularName = $this->_singularName($otherAssociationName);
							$otherOtherPluralName = $this->_pluralName($otherAssociationName);
							$formOtherOtherPluralName = 'form' . ucfirst($otherOtherPluralName);
							$actions .= "\t\t\$this->set('{$formOtherOtherPluralName}', \$this->{$currentModelName}->{$otherModelName}->{$otherOtherModelName}->find('list'));\n";
						}
					}
				}
				foreach($otherModelObj->belongsTo as $otherAssociationName => $otherRelation) {
					if(!empty($otherAssociationName)) {
						$otherOtherModelName = $this->_modelName($otherAssociationName);
						if($otherOtherModelName != $currentModelName){
							$otherOtherSingularName = $this->_singularName($otherAssociationName);
							$otherOtherPluralName = $this->_pluralName($otherAssociationName);
							$formOtherOtherPluralName = 'form' . ucfirst($otherOtherPluralName);
							$actions .= "\t\t\$this->set('{$formOtherOtherPluralName}', \$this->{$currentModelName}->{$otherModelName}->{$otherOtherModelName}->find('list'));\n";
						}
					}
				}
				foreach($otherModelObj->hasOne as $otherAssociationName => $otherRelation) {
					if(!empty($otherAssociationName)) {
						$otherOtherModelName = $this->_modelName($otherAssociationName);
						if($otherOtherModelName != $currentModelName){
							$otherOtherSingularName = $this->_singularName($otherAssociationName);
							$otherOtherPluralName = $this->_pluralName($otherAssociationName);
							$formOtherOtherPluralName = 'form' . ucfirst($otherOtherPluralName);
							$actions .= "\t\t\$this->set('{$formOtherOtherPluralName}', \$this->{$currentModelName}->{$otherModelName}->{$otherOtherModelName}->find('list'));\n";
						}
					}
				}
			}
		}
		$actions .= "\t}\n";
		$actions .= "\n";
		
		/* FORM ACTION */
		$compact = array();
		$actions .= "\tfunction {$admin}form(\$form_action = 'add', \$id = null) {\n";
		$actions .= "\t\tif(!empty(\$id)){\n";
		$actions .= "\t\t\t\${$singularName} = \$this->{$currentModelName}->read(null, \$id);\n";
		$actions .= "\t\t\t\$this->set('{$singularName}', \${$singularName});\n";
		$actions .= "\t\t}\n";
		$actions .= "\t\tif (!empty(\$this->data)) {\n";
		$actions .= "\t\t\t\$this->{$currentModelName}->create();\n";
		$actions .= "\t\t\tif (\$this->{$currentModelName}->save(\$this->data)) {\n";
		$actions .= "\t\t\t\t\$this->Session->setFlash(__('The ".$singularHumanName." has been saved', true), 'default', array(), 'info');\n";
		$actions .= "\t\t\t\tif(!empty(\$id)){\n";
		$actions .= "\t\t\t\t\t\$this->redirect(array('action' => 'view', \$id));\n";
		$actions .= "\t\t\t\t}else{\n";
		$actions .= "\t\t\t\t\t\$this->redirect(\$this->referer());\n";
		$actions .= "\t\t\t\t}\n";
		$actions .= "\t\t\t} else {\n";
		$actions .= "\t\t\t\t\$this->Session->setFlash(__('The {$singularHumanName} could not be saved. Please, try again.', true), 'default', array(), 'error');\n";
		$actions .= "\t\t\t}\n";
		$actions .= "\t\t}else{\n";
		$actions .= "\t\t\t\$this->data = array();\n";
		$actions .= "\t\t\t\$this->data['{$currentModelName}'] = array();\n";
		$actions .= "\t\t\tif(!empty(\${$singularName})){\n";
		$actions .= "\t\t\t\t\$this->data = \${$singularName};\n";
		$actions .= "\t\t\t}\n";
		$actions .= "\t\t\tforeach(\$this->passedArgs as \$fieldName => \$value){\n";
		$actions .= "\t\t\t\t\$this->data['{$currentModelName}'][\$fieldName] = \$value;\n";
		$actions .= "\t\t\t}\n";
		$actions .= "\t\t}\n";
		foreach ($modelObj->hasAndBelongsToMany as $associationName => $relation) {
			if (!empty($associationName)) {
				$habtmModelName = $this->_modelName($associationName);
				$habtmSingularName = $this->_singularName($associationName);
				$habtmPluralName = $this->_pluralName($associationName);
				$actions .= "\t\t\${$habtmPluralName} = \$this->{$currentModelName}->{$habtmModelName}->find('list');\n";
				$compact[] = "'{$habtmPluralName}'";
			}
		}
		foreach ($modelObj->belongsTo as $associationName => $relation) {
			if (!empty($associationName)) {
				$belongsToModelName = $this->_modelName($associationName);
				$belongsToPluralName = $this->_pluralName($associationName);
				//tree behaviour?
				if(is_array($modelObj->$belongsToModelName->actsAs) && in_array('Tree', $modelObj->$belongsToModelName->actsAs)){
					//$actions .= "\t\t\${$controllerPath} = \$this->{$currentModelName}->find('list');\n";
					$actions .= "\t\t\${$belongsToPluralName} = \$this->{$currentModelName}->{$belongsToModelName}->generateTreeList(null, \"{n}.{$belongsToModelName}.{$modelObj->$belongsToModelName->primaryKey}\", \"{n}.{$belongsToModelName}.{$modelObj->$belongsToModelName->displayField}\", '--', 0);\n";
				}else{
					$actions .= "\t\t\${$belongsToPluralName} = \$this->{$currentModelName}->{$belongsToModelName}->find('list');\n";
				}
				$compact[] = "'{$belongsToPluralName}'";
			}
		}
		//tree behaviour?
		if(is_array($modelObj->actsAs) && in_array('Tree', $modelObj->actsAs)){
			//$actions .= "\t\t\${$controllerPath} = \$this->{$currentModelName}->find('list');\n";
			$actions .= "\t\t\${$controllerPath} = \$this->{$currentModelName}->generateTreeList(null, \"{n}.{$currentModelName}.{$modelObj->primaryKey}\", \"{n}.{$currentModelName}.{$modelObj->displayField}\", '--', 0);\n";
			$compact[] = "'{$controllerPath}'";
		}
		//
		if (!empty($compact)) {
			$actions .= "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
		}
		$actions .= "\t\t\$form_url = '/' . \$this->params['url']['url'];\n";
		$actions .= "\t\t\$this->set('form_url', \$form_url);\n";
		$actions .= "\t\t\$this->set('form_action', \$form_action);\n";
		$actions .= "\t}\n";
		$actions .= "\n";
		

		/* ADD ACTION */
		$actions .= "\tfunction {$admin}add() {\n";
		$actions .= "\t}\n";
		$actions .= "\n";

		/* EDIT ACTION */
		$compact = array();
		$actions .= "\tfunction {$admin}edit(\$id = null) {\n";
		$actions .= "\t\t\$this->set('id', \$id);\n";
		$actions .= "\t}\n";
		$actions .= "\n";
		
		/* DELETE ACTION */		
		$actions .= "\tfunction {$admin}delete(\$id = null) {\n";
		$actions .= "\t\tif (!\$id) {\n";
		$actions .= "\t\t\t\$this->Session->setFlash(__('Invalid id for {$singularHumanName}', true), 'default', array(), 'error');\n";
		$actions .= "\t\t} else {\n";
		$actions .= "\t\t\t\$this->{$currentModelName}->del(\$id);\n";
		$actions .= "\t\t\t\$this->Session->setFlash(__('{$singularHumanName} deleted', true), 'default', array(), 'info');\n";
		$actions .= "\t\t}\n";
		$actions .= "\t\t\$this->redirect(\$this->referer(array('action'=>'index')));\n";
		$actions .= "\t}\n";
		$actions .= "\n";
		
		/* UNDELETE ACTION */		
		$actions .= "\tfunction {$admin}undelete(\$id = null) {\n";
		$actions .= "\t\tif (!\$id) {\n";
		$actions .= "\t\t\t\$this->Session->setFlash(__('Invalid id for {$singularHumanName}', true), 'default', array(), 'error');\n";
		$actions .= "\t\t} else {\n";
		$actions .= "\t\t\t\$this->{$currentModelName}->undelete(\$id);\n";
		$actions .= "\t\t\t\$this->Session->setFlash(__('{$singularHumanName} restored', true), 'default', array(), 'info');\n";
		$actions .= "\t\t}\n";
		$actions .= "\t\t\$this->redirect(\$this->referer(array('action'=>'index')));\n";
		$actions .= "\t}\n";
		$actions .= "\n";
		
		/* HARD DELETE ACTION */		
		$actions .= "\tfunction {$admin}hard_delete(\$id = null) {\n";
		$actions .= "\t\tif (!\$id) {\n";
		$actions .= "\t\t\t\$this->Session->setFlash(__('Invalid id for {$singularHumanName}', true), 'default', array(), 'error');\n";
		$actions .= "\t\t} else {\n";
		$actions .= "\t\t\t\$this->{$currentModelName}->hardDelete(\$id);\n";
		$actions .= "\t\t\t\$this->Session->setFlash(__('{$singularHumanName} deleted', true), 'default', array(), 'info');\n";
		$actions .= "\t\t}\n";
		$actions .= "\t\t\$this->redirect(\$this->referer(array('action'=>'index')));\n";
		$actions .= "\t}\n";
		$actions .= "\n";
		
		return $actions;
	}


/**
 * Assembles and writes a Controller file
 *
 * @param string $controllerName Controller name
 * @param string $actions Actions to add, or set the whole controller to use $scaffold (set $actions to 'scaffold')
 * @param array $helpers Helpers to use in controller
 * @param array $components Components to use in controller
 * @param array $uses Models to use in controller
 * @return string Baked controller
 * @access private
 */
	function bake($controllerName, $actions = '', $helpers = null, $components = null, $uses = null) {
		$out = "<?php\n";
		$out .= "class $controllerName" . "Controller extends {$this->plugin}AppController {\n\n";
		$out .= "\tvar \$name = '$controllerName';\n";

		if (low($actions) == 'scaffold') {
			$out .= "\tvar \$scaffold;\n";
		} else {
			if (count($uses)) {
				$out .= "\tvar \$uses = array('" . $this->_modelName($controllerName) . "', ";

				foreach ($uses as $use) {
					if ($use != $uses[count($uses) - 1]) {
						$out .= "'" . $this->_modelName($use) . "', ";
					} else {
						$out .= "'" . $this->_modelName($use) . "'";
					}
				}
				$out .= ");\n";
			}

			$out .= "\tvar \$helpers = array('Html', 'Form'";
			if (count($helpers)) {
				foreach ($helpers as $help) {
					$out .= ", '" . Inflector::camelize($help) . "'";
				}
			}
			$out .= ");\n";

			if (count($components)) {
				$out .= "\tvar \$components = array(";

				foreach ($components as $comp) {
					if ($comp != $components[count($components) - 1]) {
						$out .= "'" . Inflector::camelize($comp) . "', ";
					} else {
						$out .= "'" . Inflector::camelize($comp) . "'";
					}
				}
				$out .= ");\n";
			}
			$out .= $actions;
		}
		$out .= "}\n";
		$out .= "?>";
		$filename = $this->path . $this->_controllerPath($controllerName) . '_controller.php';
		return $this->createFile($filename, $out);
	}
/**
 * Assembles and writes a unit test file
 *
 * @param string $className Controller class name
 * @return string Baked test
 * @access private
 */
	function bakeTest($className) {
		$import = $className;
		if ($this->plugin) {
			$import = $this->plugin . '.' . $className;
		}
		$out = "App::import('Controller', '$import');\n\n";
		$out .= "class Test{$className} extends {$className}Controller {\n";
		$out .= "\tvar \$autoRender = false;\n}\n\n";
		$out .= "class {$className}ControllerTest extends CakeTestCase {\n";
		$out .= "\tvar \${$className} = null;\n\n";
		$out .= "\tfunction setUp() {\n\t\t\$this->{$className} = new Test{$className}();\n\t}\n\n";
		$out .= "\tfunction test{$className}ControllerInstance() {\n";
		$out .= "\t\t\$this->assertTrue(is_a(\$this->{$className}, '{$className}Controller'));\n\t}\n\n";
		$out .= "\tfunction tearDown() {\n\t\tunset(\$this->{$className});\n\t}\n}\n";

		$path = CONTROLLER_TESTS;
		if (isset($this->plugin)) {
			$pluginPath = 'plugins' . DS . Inflector::underscore($this->plugin) . DS;
			$path = APP . $pluginPath . 'tests' . DS . 'cases' . DS . 'controllers' . DS;
		}

		$filename = Inflector::underscore($className).'_controller.test.php';
		$this->out("\nBaking unit test for $className...");

		$header = '$Id';
		$content = "<?php \n/* SVN FILE: $header$ */\n/* ". $className ."Controller Test cases generated on: " . date('Y-m-d H:m:s') . " : ". time() . "*/\n{$out}?>";
		return $this->createFile($path . $filename, $content);
	}
/**
 * Outputs and gets the list of possible models or controllers from database
 *
 * @param string $useDbConfig Database configuration name
 * @return array Set of controllers
 * @access public
 */
	function listAll($useDbConfig = 'default') {
		$db =& ConnectionManager::getDataSource($useDbConfig);
		$usePrefix = empty($db->config['prefix']) ? '' : $db->config['prefix'];
		if ($usePrefix) {
			$tables = array();
			foreach ($db->listSources() as $table) {
				if (!strncmp($table, $usePrefix, strlen($usePrefix))) {
					$tables[] = substr($table, strlen($usePrefix));
				}
			}
		} else {
			$tables = $db->listSources();
		}

		if (empty($tables)) {
			$this->err(__('Your database does not have any tables.', true));
			exit();
		}

		$this->__tables = $tables;
		$this->out('Possible Controllers based on your current database:');
		$this->_controllerNames = array();
		$count = count($tables);
		for ($i = 0; $i < $count; $i++) {
			$this->_controllerNames[] = $this->_controllerName($this->_modelName($tables[$i]));
			$this->out($i + 1 . ". " . $this->_controllerNames[$i]);
		}
		return $this->_controllerNames;
	}

/**
 * Forces the user to specify the controller he wants to bake, and returns the selected controller name.
 *
 * @return string Controller name
 * @access public
 */
	function getName() {
		$useDbConfig = 'default';
		$controllers = $this->listAll($useDbConfig, 'Controllers');
		$enteredController = '';

		while ($enteredController == '') {
			$enteredController = $this->in(__("Enter a number from the list above, type in the name of another controller, or 'q' to exit", true), null, 'q');

			if ($enteredController === 'q') {
				$this->out(__("Exit", true));
				exit();
			}

			if ($enteredController == '' || intval($enteredController) > count($controllers)) {
				$this->out(__('Error:', true));
				$this->out(__("The Controller name you supplied was empty, or the number \nyou selected was not an option. Please try again.", true));
				$enteredController = '';
			}
		}

		if (intval($enteredController) > 0 && intval($enteredController) <= count($controllers) ) {
			$controllerName = $controllers[intval($enteredController) - 1];
		} else {
			$controllerName = Inflector::camelize($enteredController);
		}

		return $controllerName;
	}
/**
 * Displays help contents
 *
 * @access public
 */
	function help() {
		$this->hr();
		$this->out("Usage: cake bake controller <arg1> <arg2>...");
		$this->hr();
		$this->out('Commands:');
		$this->out("\n\tcontroller <name>\n\t\tbakes controller with var \$scaffold");
		$this->out("\n\tcontroller <name> scaffold\n\t\tbakes controller with scaffold actions.\n\t\t(index, view, add, edit, delete)");
		$this->out("\n\tcontroller <name> scaffold admin\n\t\tbakes a controller with scaffold actions for both public and Configure::read('Routing.admin')");
		$this->out("\n\tcontroller <name> admin\n\t\tbakes a controller with scaffold actions only for Configure::read('Routing.admin')");
		$this->out("");
		exit();
	}
}
?>