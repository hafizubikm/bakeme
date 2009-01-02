<?php
define("CONSTANT_Namespace", 0x08);
define("CONSTANT_PackageNamespace", 0x16);
define("CONSTANT_PackageInternalNs", 0x17);
define("CONSTANT_ProtectedNamespace", 0x18);
define("CONSTANT_ExplicitNamespace", 0x19);
define("CONSTANT_StaticProtectedNs", 0x1A);
define("CONSTANT_PrivateNs", 0x05);

class namespace_info extends swftype
{
	var /* u8 */ $kind;
	var /* u30 */ $name;
	function namespace_info($kind, $name)
	{
		parent::swftype();
		$this->kind = new U8($kind);
		$this->name = new U30($name);
	}
	
	function getPacked()
	{
		return $this->kind->getPacked() . $this->name->getPacked();
	}
}


class ns_set_info extends swftype
{
	var /* u30 */ $count;
	var /* u30[] */ $ns;
	
	function ns_set_info($items=null)
	{
		parent::swftype();
		$this->count = new U30(0);
		$this->ns = new abcArray($this->count);
		if ($items!=null)
		{
			foreach($items as $item)
				$this->ns->add(new U30($item));
		}
		
	}
	function add($item)
	{
		$this->ns->add(new U30($item));
	}
}
?>