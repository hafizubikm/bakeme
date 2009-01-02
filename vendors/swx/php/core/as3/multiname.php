<?php
include_once('abcArray.php');

define("CONSTANT_QName", 0x07);
define("CONSTANT_QNameA", 0x0D);
define("CONSTANT_RTQName", 0x0F);
define("CONSTANT_RTQNameA", 0x10);
define("CONSTANT_RTQNameL", 0x11);
define("CONSTANT_RTQNameLA", 0x12);
define("CONSTANT_Multiname", 0x09);
define("CONSTANT_MultinameA", 0x0E);
define("CONSTANT_MultinameL", 0x1B);
define("CONSTANT_MultinameLA", 0x1C);

class multiname extends abcArray
{
	
	function addQname($ns_name, $string, &$constantPool)
	{
		$name = '';
		if ($ns_name!='_blank_')
			$name = $ns_name;
		$name .= ":".$string;
		$this->add(new multiname_info(CONSTANT_QName, 
									  new multiname_kind_QName($constantPool->namespace->getIndexOf($ns_name),
									  						   $constantPool->string->getIndexOf($string))
									  ),$name);
		return $this;
		
	}
}

class multiname_info extends swftype
{
	var /* u8 */ $kind;
	var /* u8[] */ $data;
	
	function multiname_info($kind, $data)
	{
		parent::swftype();
		$this->kind = new U8($kind);
		$this->data = $data;
		
	}
		
	function getPacked()
	{
		return $this->kind->getPacked() . $this->data->getPacked();	
	}

}
/* QName 
	The multiname_kind_QName format is used for kinds CONSTANT_QName and CONSTANT_QNameA.
	
	The ns and name fields are indexes into the namespace and string arrays of the constant_pool entry,
	respectively. A value of zero for the ns field indicates the any (Ò*Ó) namespace, and a value of zero for the name
	field indicates the any (Ò*Ó) name.
* */
class multiname_kind_QName extends swftype
{
	var /* u30 */ $ns;
	var /* u30 */ $name;
	
	function multiname_kind_QName($ns, $name)
	{
		$this->ns = new U30($ns);
		$this->name = new U30($name);
	}
	
	function getPacked()
	{
		return $this->ns->getPacked() . $this->name->getPacked();	
	}
}


/* RTQName
	The multiname_kind_RTQName format is used for kinds CONSTANT_RTQName and CONSTANT_RTQNameA.

	The single field, name, is an index into the string array of the constant pool. A value of zero indicates the any
	(Ò*Ó) name.

*/
class multiname_kind_RTQName extends swftype
{
	var /* u30 */ $name;
	
	function multiname_kind_RTQName($name)
	{
		$this->name = new U30($name);
	}
	
	function getPacked()
	{
		return $this->name->getPacked();	
	}
}

/* RTQNameL
	The multiname_kind_RTQNameL format is used for kinds CONSTANT_RTQNameL and CONSTANT_RTQNameLA.

This kind has no associated data.
*/
class multiname_kind_RTQNameL extends swftype
{
	
	function multiname_kind_RTQName()
	{
		
	}
	
	function getPacked()
	{
		return '';	
	}
}

/* Multiname
	The multiname_kind_Multiname format is used for kinds CONSTANT_Multiname and CONSTANT_MultinameA.

	The name field is an index into the string array, and the ns_set field is an index into the ns_set array. A
	value of zero for the name field indicates the any (Ò*Ó) name. The value of ns_set cannot be zero.
*/
class multiname_kind_Multiname extends swftype
{
	var /* u30 */ $name;
	var /* u30 */ $ns_set;
	
	function multiname_kind_RTQName($name, $ns_set)
	{
		$this->name = new U30($name);
		$this->ns_set = new U30($ns_set);
	}
	
	function getPacked()
	{
		return $this->name->getPacked() . $this->ns_set->getPacked();	
	}
}
/* MultinameL
	The multiname_kind_MultinameL format is used for kinds CONSTANT_MultinameL and CONSTANT_MultinameLA.

	The ns_set field is an index into the ns_set array of the constant pool. The value of ns_set cannot be zero.
*/
class multiname_kind_MultinameL extends swftype
{
	var /* u30 */ $ns_set;
	
	function multiname_kind_RTQName($ns_set)
	{
		$this->ns_set = new U30($ns_set);
	}
	
	function getPacked()
	{
		return $this->name->getPacked() . $this->ns_set->getPacked();	
	}
}

?>
