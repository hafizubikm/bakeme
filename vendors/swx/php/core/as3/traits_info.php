<?php
define("Trait_Slot", 0);
define("Trait_Method", 1);
define("Trait_Getter", 2);
define("Trait_Setter", 3);
define("Trait_Class", 4);
define("Trait_Function", 5);
define("Trait_Const", 6);

define("SLOT_ID_AUTO",0);
define("SLOT_TYPE_ANY",0);

define("ATTR_NoAttribute", 0x0); // Is used with Trait_Method, Trait_Getter and Trait_Setter. It marks a method that cannot be overridden by a sub-class
define("ATTR_Final", 0x1); // Is used with Trait_Method, Trait_Getter and Trait_Setter. It marks a method that cannot be overridden by a sub-class
define("ATTR_Override", 0x2); // Is used with Trait_Method, Trait_Getter and Trait_Setter. It marks a method that has been overridden in this class
define("ATTR_Metadata", 0x4); // Is used to signal that the fields metadata_count and metadata follow the data field in the traits_info entry

class traits_info extends swftype
{
	var /* u30 */ $name;
	var /* u8 */ $kind;
	var /* u8[] */ $data;
	var /* u30 */ $metadata_count;
	var /* u30[] */ $metadata;
	
	function traits_info($name, $kind, $data, $attribute = ATTR_NoAttribute)
	{
		parent::swftype();		
		$this->name = new U30($name);
		if ($attribute!=ATTR_NoAttribute)
			$this->kind = new U8( $kind & ($attribute << 4) );
		else
			$this->kind = new U8($kind);
		$this->data = $data;
	}
	
	function getPacked()
	{
		return $this->name->getPacked() . $this->kind->getPacked() . $this->data->getPacked() ; // ignore metadata for now  
	}
	
/*
	static function makeSlot($name, $slot_id, $type_name, $vindex, $vkind, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_slot($slot_id, $type_name, $vindex, $vkind);		
		return new traits_info($name, Trait_Slot, $data, $attribute);
	}
	static function makeConstant($name, $slot_id, $type_name, $vindex, $vkind, $attribute = ATTR_NoAttribute)
	{
		// same as trait_slot
		$data = new trait_slot($slot_id, $type_name, $vindex, $vkind);		
		return new traits_info($name, Trait_Const, $data, $attribute);
	}
	
	static function makeMethod($name, $disp_id, $method, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_method($disp_id, $method);
		return new traits_info($name, Trait_Method, $data, $attribute);
	}
	static function makeSetter($name, $disp_id, $method, $attribute = ATTR_NoAttribute)
	{
		// same as trait_method
		$data = new trait_method($disp_id, $method);
		return new traits_info($name, Trait_Setter, $data, $attribute);
	}
	static function makeGetter($name, $disp_id, $method, $attribute = ATTR_NoAttribute)
	{
		// same as trait_method
		$data = new trait_method($disp_id, $method);
		return new traits_info($name, Trait_Getter, $data, $attribute);
	}
	
	static function makeClass($name,$slot_id, $classi, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_class($slot_id, $classi);
		return new traits_info($name, Trait_Class, $data, $attribute);
	}
	
	static function makeFunction($name,$slot_id, $function, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_function($slot_id, $function);
		return new traits_info($name, Trait_Function, $data, $attribute);
	}

*/
}


class trait_slot extends swftype
{
	var /* u30 */ $slot_id;
	var /* u30 */ $type_name;
	var /* u30 */ $vindex;
	var /* u8 */ $vkind;
	
	function trait_slot($slot_id, $type_name, $vindex, $vkind)
	{
		parent::swftype();
		$this->slot_id = new U30($slot_id);
		$this->type_name = new U30($type_name);
		$this->vindex = new U30($vindex);
		$this->vkind = new U8($vkind);
	}
	
	function getPacked()
	{
		$s =  $this->slot_id->getPacked() . $this->type_name->getPacked() . $this->vindex->getPacked();
		if ($this->vindex->get()!=0)
			$s .= $this->vkind->getPacked() ;
		return $s; 
	}
}

class trait_method extends swftype
{
	var /* u30 */ $disp_id;
	var /* u30 */ $method;
	
	function trait_method($disp_id, $method)
	{
		parent::swftype();
		$this->disp_id = new U30($disp_id);
		$this->method = new U30($method);
	}
	
	function getPacked()
	{
		return $this->disp_id->getPacked() . $this->method->getPacked(); 
	}
	
	
}


class trait_class extends swftype
{
	var /* u30 */ $slot_id;
	var /* u30 */ $classi;
	
	function trait_class($slot_id, $classi)
	{
		parent::swftype();
		$this->slot_id = new U30($slot_id);
		$this->classi = new U30($classi);
	}
	
	function getPacked()
	{
		return $this->slot_id->getPacked() . $this->classi->getPacked(); 
	}
	
	
}



class trait_function extends swftype
{
	var /* u30 */ $slot_id;
	var /* u30 */ $function;
	
	function trait_method($slot_id, $function)
	{
		parent::swftype();
		$this->slot_id = new U30($slot_id);
		$this->function = new U30($function);
	}
	
	function getPacked()
	{
		return $this->slot_id->getPacked() . $this->function->getPacked(); 
	}
	
	
}



?>