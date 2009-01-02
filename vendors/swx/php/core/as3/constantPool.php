<?php


class constantPool extends swftype
{
	var /* U30 */ $int_count;
	var /* s32[] */ $integer;
	
	var /* u30 */ $uint_count;
	var /* u32[] */ $uinteger;
	
	var /* u30 */ $double_count;
	var /* d64 */ $double;
	
	var /* u30 */ $string_count;
	var /* string_info[] */ $string;
	
	var /* u30 */ $namespace_count;
	var /* namespace_info[] */ $namespace;
	
	var /* u30 */ $ns_set_count;
	var /* ns_set_info[] */ $ns_set;
	
	var /* u30 */ $multiname_count;
	var /* multiname_info[] */ $multiname;

	function constantPool()
	{
		parent::swftype();
		$this->int_count = new U30(0);
		$this->integer = new abcArray($this->int_count, TRUE);
		$this->uint_count = new U30(0);
		$this->uinteger = new abcArray($this->uint_count, TRUE);
		$this->double_count = new U30(0);
		$this->double = new abcArray($this->double_count, TRUE);
		$this->string_count = new U30(0);
		$this->string = new abcArray($this->string_count, TRUE);
		$this->namespace_count = new U30(0);
		$this->namespace = new abcArray($this->namespace_count, TRUE);
		$this->ns_set_count = new U30(0);
		$this->ns_set = new abcArray($this->ns_set_count, TRUE);
		$this->multiname_count = new U30(0);
		$this->multiname = new multiname($this->multiname_count, TRUE);

	
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
	
	function printStats()
	{
		echo "<blockquote>";
		echo "Constant Pool stats:<br/><br/>";
		
		echo "int_count=".$this->int_count->get()."<br/>";
		echo "count(integer)=".($this->integer->count())."<br/>";
		
		echo "uint_count=".$this->uint_count->get()."<br/>";
		echo "count(uinteger)=".($this->uinteger->count())."<br/>";
		
		echo "double_count=".$this->double_count->get()."<br/>";
		echo "count(double)=".($this->double->count())."<br/>";
		
		echo "string_count=".$this->string_count->get()."<br/>";
		echo "count(string)=".($this->string->count())."<br/>";
		
		echo "namespace_count=".$this->namespace_count->get()."<br/>";
		echo "count(namespace)=".($this->namespace->count())."<br/>";
		
		echo "ns_set_count=".$this->ns_set_count->get()."<br/>";
		echo "count(ns_set)=".($this->ns_set->count())."<br/>";
		
		echo "multiname_count=".$this->multiname_count->get()."<br/>";
		echo "count(multiname)=".($this->multiname->count())."<br/>";
		
		echo "</blockquote>";
	}
}
?>
