<?php
define("INSTANCE_NOBASECLASS",0x00);

define("CONSTANT_ClassSealed", 0x01); // The class is sealed: properties can not be dynamically added to instances of the class.
define("CONSTANT_ClassFinal", 0x02); // The class is final: it cannot be a base class for any other class.
define("CONSTANT_ClassInterface", 0x04);  // The class is an interface.
define("CONSTANT_ClassProtectedNs", 0x08); // The class uses its protected namespace and the protectedNs field is present in the interface_info structure.

class instance_info extends swftype
{
	var /* U30 */ $name;
	var /* U30 */ $super_name;
	var /* U8 */ $flags;
	var /* U30 */ $protectedNs;
	var /* U30 */ $intrf_count;
	var /* U30[] */ $interface;
	var /* u30 */ $iinit;
	var /* u30 */ $trait_count;
	var /* traits_info[] */ $trait;
	
	function instance_info($name, $super_name, $flags, $protectedNs, $interfaces, $iinit, $traits)
	{
		parent::swftype();
		$this->name = new U30($name);
		$this->super_name = new U30($super_name);
		$this->flags = new U8($flags);
		$this->protectedNs = new U30($protectedNs);
		$this->intrf_count = new U30(0);
		$this->interface = new abcArray($this->intrf_count);
		foreach($interfaces as $interface)
			$this->interface->add(new U30($interface));
			
		$this->iinit = new U30($iinit);
		$this->trait_count = new U30(0);
		$this->trait = new abcArray($this->trait_count);
		foreach($traits as $trait)
			$this->trait->add($trait);
		

	}
	
	function getPacked()
	{
		$s = $this->name->getPacked() . $this->super_name->getPacked() . $this->flags->getPacked();
		if ($this->flags->get() & CONSTANT_ClassProtectedNs != 0)
			$s .= $this->protectedNs->getPacked();
		$s .= $this->intrf_count->getPacked() . $this->interface->getPacked() . $this->iinit->getPacked() . $this->trait_count->getPacked() . $this->trait->getPacked();
		return $s;
	}
}



?>
