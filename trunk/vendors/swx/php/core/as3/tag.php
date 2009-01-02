<?php
class Tag
{
	// Header
	var /* U16  */ $TagCodeAndLength;
	var /* U32 ? doc says different things */ $Length;
	
	function Tag()
	{
		$this->TagCodeAndLength = new U16(0);
		$this->Length = null;
	}

	function setTagType($typenumber)
	{
		$val = $typenumber << 6;
		$this->TagCodeAndLength->set($this->TagCodeAndLength->get() | $val) ;
	}
	
	function setTagSize($size)
	{
		if ($size >= 63)
		{
			$this->TagCodeAndLength->set($this->TagCodeAndLength->get() | 0x3f) ;
			$this->Length = new U32($size);
		}
		else
		{
			$this->TagCodeAndLength->set($this->TagCodeAndLength->get() | $size);
			
		}
	}
	
	function getTagContent()
	{
		
		$packed = $this->getPacked();
		$length = strlen($packed);
		$this->setTagSize($length);

		if ($length >= 63)
			return $this->TagCodeAndLength->getPacked() . $this->Length->getPacked() . $packed;
		else
			return $this->TagCodeAndLength->getPacked() . $packed;		
	}
	
	function dump()
	{
		echo "<hr/>Tag ID ".($this->TagCodeAndLength->get() >> 6)."<br/>";
		 
		echo "Tag length: ";
		if ($this->Length != null)
			echo $this->Length->get();
		else
			echo $this->TagCodeAndLength->get() & 63;
		echo "<br/>";
		
	}
}
?>