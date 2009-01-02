<?php
class abcArray 
{
	var $map;
	var $items;
	
	var $counter, $plusonemode;
	function abcArray(&$linkedCounter=null, $plusonemode = FALSE)
	{
		$this->map = array();
		$this->items = array();
		$this->counter = $linkedCounter;
		$this->plusonemode = $plusonemode;
		if ($this->counter!=null)
		{
			$this->counter->set(0);
		}
		
	}
		
	
	function add($value, $name = null)
	{
		$index = count($this->items);
		$this->items[] = $value;

		
		if ($name!==null)
			$this->map[$name] = $index;
		
		if ($this->counter!=null)
		{
			$this->counter->set($index+ ($this->plusonemode ? 2 : 1) );
		}
		
		return $index+ ($this->plusonemode ? 1 : 0);
		
	}
	
	function getAt($index)
	{
		return $this->items[$index];		
	}
	
	function getAtName($name)
	{
		return $this->items[$this->map[$name]];
	}
	
	function getIndexOf($name)
	{
		if (isset($this->map[$name]))
			return $this->map[$name] + ($this->plusonemode ? 1 : 0);
		else
			return FALSE;
	}
	
	function getByteLength()
	{
		return strlen($this->getPacked());
	}
	
	function getPacked()
	{
		$s = '';
		foreach($this->items as $item)
		{
			$s .= $item->getPacked();
		}
		return $s;
		
	}

	function count()
	{
		return count($this->items);
	}
}
?>
