<?php
class asmUtility
{
	
	/* Helper functions */
	function constructsuper($arg)
	{
		return new instruction(op_constructsuper,new U30($arg));
	}
	
	function findpropstrict($arg)
	{
		return new instruction(op_findpropstrict,new U30($arg));
	}
	
	function getlex($arg)
	{
		return new instruction(op_getlex,new U30($arg));
	}
	
	function callpropvoid($index,$arg_count)
	{
		return new instruction(op_callpropvoid,new U30($index),new U30($arg_count));
	}
	
	function findproperty($arg)
	{
		return new instruction(op_findproperty,new U30($arg));
	}
	
	function initproperty($arg)
	{
		return new instruction(op_initproperty,new U30($arg));
	}
	
	function setproperty($arg)
	{
		return new instruction(op_setproperty,new U30($arg));
	}
	
	function newclass($arg)
	{
		return new instruction(op_newclass,new U30($arg));
	}
	
	
	function makeSlot($name, $slot_id, $type_name, $vindex, $vkind, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_slot($slot_id, $type_name, $vindex, $vkind);		
		return new traits_info($name, Trait_Slot, $data, $attribute);
	}
	function makeConstant($name, $slot_id, $type_name, $vindex, $vkind, $attribute = ATTR_NoAttribute)
	{
		// same as trait_slot
		$data = new trait_slot($slot_id, $type_name, $vindex, $vkind);		
		return new traits_info($name, Trait_Const, $data, $attribute);
	}
	
	function makeMethod($name, $disp_id, $method, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_method($disp_id, $method);
		return new traits_info($name, Trait_Method, $data, $attribute);
	}
	function makeSetter($name, $disp_id, $method, $attribute = ATTR_NoAttribute)
	{
		// same as trait_method
		$data = new trait_method($disp_id, $method);
		return new traits_info($name, Trait_Setter, $data, $attribute);
	}
	function makeGetter($name, $disp_id, $method, $attribute = ATTR_NoAttribute)
	{
		// same as trait_method
		$data = new trait_method($disp_id, $method);
		return new traits_info($name, Trait_Getter, $data, $attribute);
	}
	
	function makeClass($name,$slot_id, $classi, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_class($slot_id, $classi);
		return new traits_info($name, Trait_Class, $data, $attribute);
	}
	
	function makeFunction($name,$slot_id, $function, $attribute = ATTR_NoAttribute)
	{
		$data = new trait_function($slot_id, $function);
		return new traits_info($name, Trait_Function, $data, $attribute);
	}	
	
	// Aral: for LocalConnection 
	function constructprop($index, $arg_count)
	{
		return new instruction(op_constructprop,new U30($index),new U30($arg_count));
	}
	
	function coerce($index)
	{
		return new instruction(op_coerce, new U30($index));
	}
	
	function newobject($arg_count)
	{
		return new instruction(op_newobject,new U30($arg_count));
	}
	
}
?>