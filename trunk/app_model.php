<?php
class AppModel extends Model{
	
	var $actsAs = array('Containable', 'ExtendAssociations', 'SoftDeletable');
	var $language_id;
	
	function getFilters($passedArgs){
		$filters = array();
		foreach($passedArgs as $fieldName => $value){
			if(!empty($value))
			{
				//split
				$split = split('\.', $fieldName);
				if(sizeof($split) == 2){
					$modelName = $split[0];
					$fieldName = $split[1];
				}else{
					$modelName = $this->name;
				}
				if($this->hasField($fieldName) || (!empty($this->belongsTo)) && !empty($this->belongsTo[$modelName])){
					switch($this->getColumnType($fieldName))
					{
						case "string":
							$filters[] = array($modelName . '.' . $fieldName . ' LIKE ' => '%' . $value . '%');
							break;
						default:
							$filters[] = array($modelName . '.' . $fieldName => $value);
							break;
					}
				}else if(!empty($this->hasAndBelongsToMany)){
					foreach($this->hasAndBelongsToMany as $associationname => $association){
						if($association['associationForeignKey'] == $fieldName){
							$ids = $this->getIdsForHABTMCondition($associationname, $value);
							if(!empty($ids)) $filters[] = array($this->name.'.id' => $ids);
						}
					}
				}
			}
		}
		return $filters;
	}
	
	function getIdsForHABTMCondition($otherModelName, $otherModelId){
		$association = $this->hasAndBelongsToMany[$otherModelName];
		$sql = "SELECT {$association['foreignKey']} AS id FROM {$association['joinTable']} AS {$this->name} WHERE {$this->name}.{$association['associationForeignKey']} = '{$otherModelId}'";
		$result = $this->query($sql);
		$ids = array();
		foreach($result as $value){
			$ids[] = $value[$this->name]['id'];
		}
		return $ids;
	}
	
	/** 
	* Private, recursive helper method for findAllThreaded. 
	* 
	* @param array $data Results of find operation 
	* @param string $root NULL or id for root node of operation 
	* @return array Threaded results 
	* @access private 
	* @see Model::findAllThreaded() 
	*/ 
	function __doThread($data, $root) { 
		$out = array(); 
		$sizeOf = sizeof($data);
		for ($ii = 0; $ii < $sizeOf; $ii++) { 
			if (($data[$ii][$this->alias]['parent_id'] == $root) || (($root === null) && ($data[$ii][$this->alias]['parent_id'] == '0'))) { 
				$tmp = $data[$ii]; 
				if (isset($data[$ii][$this->alias][$this->primaryKey])) { 
					$tmp['children'] = $this->__doThread($data, $data[$ii][$this->alias][$this->primaryKey]);
				} else { 
					$tmp['children'] = null;
				} 
				$out[] = $tmp;
			}
		} 
		return $out;
	}
}
?>