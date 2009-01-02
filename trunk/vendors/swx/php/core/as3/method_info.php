<?php
/*
NEED_ARGUMENTS 0x01 Suggests to the run-time that an ÒargumentsÓ object (as specified by the
ActionScript 3.0 Language Reference) be created. Must not be used
together with NEED_REST. See Chapter 3.
NEED_ACTIVATION 0x02 Must be set if this method uses the newactivation opcode.
NEED_REST 0x04 This flag creates an ActionScript 3.0 rest arguments array. Must not be
used with NEED_ARGUMENTS. See Chapter 3.
HAS_OPTIONAL 0x08 Must be set if this method has optional parameters and the options
field is present in this method_info structure.
SET_DXNS 0x40 Must be set if this method uses the dxns or dxnslate opcodes.
HAS_PARAM_NAMES 0x80 Must be set when the param_names field is present in this method_info
structure.
*/
define("NO_FLAG",0x00);
define("NEED_ARGUMENTS",0x01);
define("NEED_ACTIVATION",0x02);
define("NEED_REST",0x04);
define("HAS_OPTIONAL",0x08);
define("SET_DXNS",0x40);
define("HAS_PARAM_NAMES",0x80);

define("CONSTANT_Int", 0x03); //integer
define("CONSTANT_UInt", 0x04); //uinteger
define("CONSTANT_Double", 0x06); //double
define("CONSTANT_Utf8", 0x01); //string
define("CONSTANT_True", 0x0B); // -
define("CONSTANT_False", 0x0A); // -
define("CONSTANT_Null", 0x0C); // -
define("CONSTANT_Undefined", 0x00); // -


define("CONSTANT_ReturnAny", 0x00); // *

define("CONSTANT_AnonymousMethod", 0x00); // *

class method_info extends swftype
{
	var /* U30 */ $param_count;
	var /* U30 */ $return_type;
	var /* U30[] */ $param_type;
	var /* U30 */ $name;
	var /* U8 */ $flags;
	var /* option_info */ $options;
	var /* U30[] */ $param_names;
	
	/*u30 param_count
	u30 return_type
	u30 param_type[param_count]
	u30 name
	u8 flags
	option_info options
	param_info param_names*/
	
	function method_info($name, $return_type, $parameters, $flags, $options=null)
	{
		$this->param_count = new U30(0);
		$this->return_type = new U30($return_type);
		$this->param_type = new abcArray($this->param_count);
		$this->param_names = new abcArray();
		foreach($parameters as $index => $type)
		{
			$this->param_type->add($type);
			if ($flags & HAS_PARAM_NAMES != 0)
				$this->param_names->add(new U30($index));
		}
		$this->name = new U30($name);
		$this->flags = new U8($flags);
		
		$this->options = new option_info();
		if ($options!=null)
			foreach($options as $option)
			{
				$this->options->add($option['val'],$option['kind']);
			}
		
	}
	
	function getPacked()
	{
		$s = $this->param_count->getPacked() . $this->return_type->getPacked() . $this->param_type->getPacked() . $this->name->getPacked() . $this->flags->getPacked();
		
		if ($this->flags->get() & HAS_OPTIONAL != 0)
				$s .= $this->options->getPacked();
		
		if ($this->flags->get() & HAS_PARAM_NAMES != 0)
				$s .= $this->param_names->getPacked();
		
		return $s;
	}
}

class option_info extends swftype
{
	var /* U30 */ $option_count;
	var /* option_detail[] */ $option;
	
	function option_info()
	{
		parent::swftype();
		$this->option_count = new U30(0);
		$this->option = new abcArray($this->option_count);
	}
	
	function add($val, $kind)
	{
		$this->option->add(new option_detail($val,$kind));
	}

	function getPacked()
	{
		return $this->option_count->getPacked() . $this->option->getPacked();
	}
}

class option_detail extends swftype
{
	var /* U30 */ $val;
	var /* U8 */ $kind;
	function option_detail($val, $kind)
	{
		$this->val = new U30($val);
		$this->kind = new U8($kind);
	}
	
	function getPacked()
	{
		return $this->val->getPacked() . $this->kind->getPacked();
	}
}


?>
