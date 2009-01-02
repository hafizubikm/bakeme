<?php
class method_body_info extends swftype
{
	var /* u30 */ $method;
	var /* u30 */ $max_stack;
	var /* u30 */ $local_count;
	var /* u30 */ $init_scope_depth;
	var /* u30 */ $max_scope_depth;
	var /* u30 */ $code_length;
	var /* u8[code_length] */ $code;
	var /* u30 */ $exception_count;
	var /* exception_info[exception_count] */ $exception;
	var /* u30 */ $trait_count;
	var /* traits_info[trait_count]*/ $trait;
	
	function method_body_info($method, $max_stack, $local_count, $init_scope_depth, $max_scope_depth, $code, $exceptions, $traits)
	{
		parent::swftype();
		
		$this->method = new U30($method);
		$this->max_stack = new U30($max_stack);
		$this->local_count = new U30($local_count);
		$this->init_scope_depth = new U30($init_scope_depth);
		$this->max_scope_depth = new U30($max_scope_depth);
			
		$this->code_length = new U30(0);
		$this->code = new abcArray();
		foreach($code as $instruction)
			$this->code->add($instruction);
		
		$this->exception_count = new U30(0);
		$this->exception = new abcArray($this->exception_count);
		foreach($exceptions as $exception)
			$this->exception->add(new U30($exception));
		
		$this->trait_count = new U30(0);
		$this->trait = new abcArray($this->trait_count);
		foreach($traits as $trait)
			$this->trait->add(new U30($trait));
		
	}
	
	function getPacked()
	{
		$this->code_length->set($this->code->getByteLength());
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
