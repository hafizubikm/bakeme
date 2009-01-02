<?php
class exception_info extends swftype
{
	var /* u30 */ $from;
	var /* u30 */ $to;
	var /* u30 */ $target;
	var /* u30 */ $exc_type;
	var /* u30 */ $var_name;
	
	function exception_info($from, $to, $target, $exc_type, $var_name)
	{
		parent::swftype();
		$this->from = new U30($from);
		$this->to = new U30($to);
		$this->target = new U30($target);
		$this->exc_type = new U30($exc_type);
		$this->var_name = new U30($var_name);
		
	}
	
	function getPacked()
	{
		$s = '';
		foreach ($this as $prop => $value)
		{
			if ($prop != 'data')
			{
				$s .= $value->getPacked();
			}
		}
		return $s;
	}
}
?>