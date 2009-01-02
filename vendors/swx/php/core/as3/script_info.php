<?php
class script_info extends swftype
{
	var /* u30 */ $init;
	var /* u30 */ $trait_count;
	var /* traits_info[]*/ $trait;
	
	function script_info($init, $traits)
	{
		parent::swftype();
		$this->init = new U30($init);
		$this->trait_count = new U30(0);
		$this->trait = new abcArray($this->trait_count);
		foreach($traits as $trait)
			$this->trait->add($trait);
	}

	function getPacked()
	{
		return $this->init->getPacked() . $this->trait_count->getPacked() . $this->trait->getPacked();
	}
}
?>
