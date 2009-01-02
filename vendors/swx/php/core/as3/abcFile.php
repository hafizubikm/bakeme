<?php

define("ABC_MAJOR",46);
define("ABC_MINOR",16);

class abcFile extends swftype
{
	var /*U16*/ $minor_version;
	var /*U16*/ $major_version;
	
	var /*cpool_info*/ $constant_pool;
	
	var /*U30*/ $method_count;
	var /*method_info*/ $method;
	
	var /*U30*/ $metadata_count;
	var /*method_info*/ $metadata;
	
	var /*U30*/ $class_count;
	var /*instance_info*/ $instance;
	var /*class_info*/ $class;
	
	var /*U30*/ $script_count;
	var /*method_info*/ $script;

	var /*U30*/ $method_body_count;
	var /*method_info*/ $method_body;
	
	
	function abcFile()
	{
		parent::swftype();
		$this->minor_version = new U16(ABC_MINOR);
		$this->major_version = new U16(ABC_MAJOR);
		$this->constant_pool = new constantPool();
		$this->method_count = new U30(0);
		$this->method = new abcArray($this->method_count);
		$this->metadata_count = new U30(0);
		$this->metadata = new abcArray($this->metadata_count);
		$this->class_count = new U30(0);
		$this->instance = new abcArray($this->class_count);
		$this->class = new abcArray();
		$this->script_count = new U30(0);
		$this->script = new abcArray($this->script_count);
		$this->method_body_count = new U30(0);
		$this->method_body = new abcArray($this->method_body_count);
	}
	
	function getPacked()
	{
		$s = '';
		foreach ($this as $prop => $value)
		{
			if ($prop != 'data')
			{
				$s .= $value->getPacked();
			}
		}
		return $s;
	}
	
	
}
?>
