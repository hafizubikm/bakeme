<?php
define("op_getlocal0",0xD0);
define("op_pushscope",0x30);
define("op_returnvoid",0x47);
define("op_constructsuper",0x49);
define("op_findpropstrict",0x5D);
define("op_pushbyte",0x24);
define("op_getlex",0x60);
define("op_callpropvoid",0x4F);
define("op_findproperty",0x5E);
define("op_initproperty",0x68);
define("op_getscopeobject",0x65);
define("op_newclass",0x58);
define("op_popscope",0x1D);
define("op_pushint",0x2D);
define("op_newarray",0x56);
define("op_newobject",0x55);
define("op_pushstring",0x2c);
define("op_pushtrue",0x26);
define("op_pushfalse",0x27);
define("op_pushdouble",0x2f);
define("op_pushnull",0x20);
define("op_setproperty",0x61);
// Aral: additional opcodes to support LocalConnection for Debug calls.
define("op_coerce", 0x80);
define("op_getlocal1", 0xD1);
define("op_setlocal1", 0xD5);
define("op_constructprop", 0x4A);


/*define("op_",0x);
define("op_",0x);*/

class instruction extends swftype
{
	var /* U8 */ $code, $arg1, $arg2, $arg3, $arg4;
	
	function instruction($code, $arg1=null, $arg2=null, $arg3=null, $arg4=null)
	{
		parent::swftype();	
		$this->code = new U8($code);
		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
		$this->arg3 = $arg3;
		$this->arg4 = $arg4;
	}
	
	function getPacked()
	{
		$s = '';
		foreach ($this as $prop => $value)
		{
			//echo "->$prop<br/>";
			if ($prop != 'data' && $value !== null)
			{
				$s .= $value->getPacked();
			}
		}
		return $s;
	}

}

?>