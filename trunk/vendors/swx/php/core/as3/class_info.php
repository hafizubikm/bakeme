<?php
class class_info extends swftype
{
	var /* u30 */ $cinit;
	var /* u30 */ $trait_count;
	var /* traits_info[] */ $traits;	
	
	function class_info($cinit, $traits) 
	{
		parent::swftype();
		$this->cinit = new U30($cinit);
		$this->trait_count = new U30(0);
		$this->traits = new abcArray($this->trait_count);
		foreach($traits as $trait)
			$this->traits->add($trait);
			
	}
	
	function getPacked()
	{
		return $this->cinit->getPacked() . $this->trait_count->getPacked() . $this->traits->getPacked();
	}
}


?>
