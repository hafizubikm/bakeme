<?php

/* swftype library developped for swxformat.
 * 
 * Some AMV2 formatting functions by Richard Lord. See AVM2_Numbers.php for License
 * 
*/
include "AVM2_Numbers.php";



class swftype 
{
	var $data;
	function swftype($value=null)
	{
		$data = 0;
		if ($value !== null)
			$this->set($value);
	}
	
	function set($value)
	{
		$this->data = $value;
	}
	
	function get()
	{
		return $this->data;		
	}
	
	function getPacked()
	{
		trigger_error('class must implement getPacked().', E_ERROR);
	}
	
	function dump()
	{
		echo "<pre>".print_r($this,true)."</pre>";
		
	}
}

class U16 extends swftype
{
	
	function getPacked()
	{
		return AVM2_toU16($this->data);
	}
	
}

class U8 extends swftype
{
	function getPacked()
	{
		return AVM2_toU8($this->data);
	}
}

class U32 extends swftype
{
	function getPacked()
	{
		/* Not sure here AVM2_toU32 didn't seem to give the correct result. pack does */
		//return AVM2_toU32($this->data);
		return pack('V',$this->data);
	}
}

class U30 extends swftype
{
	function getPacked()
	{
		return AVM2_toU30($this->data);
	}	
}

class S32 extends swftype
{
	function getPacked()
	{
		return AVM2_toS32($this->data);
	}
}

class S24 extends swftype
{
	function getPacked()
	{
		return AVM2_toS24($this->data);
	}
}

class D64 extends swftype
{
	function getPacked()
	{
		return AVM2_toD64($this->data);
	}	
}


class BitStream extends swftype
{
	var $stream;
	var $map;
	
	function BitStream($variables=null)
	{
		
		parent::swftype();
		$this->stream = array();
		$this->map = array();
		if ($variables !== null)
			foreach($variables as $variable)
			{
				$this->addVar($variable['name'], $variable['length'],$variable['type'], $variable['value']);
			}
	}
	
	/* $type = one in 'S' (signed), 'U' (unsigned), 'F' (fixed point) */
	function addVar($name,$length,$type, $value=0)
	{
		if ($name == 'Reserved' || !isset($this->map[$name]))
		{
			$index = count($this->stream);
			
			$this->stream[]=array('name'=>$name,'length'=>$length, 'type'=>$type, 'value'=>$value);
			$this->map[$name] = $index;
		}
		else
		{
			$this->stream[$this->map[$name]]->value = $value;
		}
	}
	
	function setVar($name,$value)
	{
		if (isset($this->map[$name]))
			$this->stream[$this->map[$name]]->value = $value;
		else
			trigger_error("setVar: $name is not a defined variable", E_ERROR);
	}
	
	function getVar($name)
	{
		if (isset($this->map[$name]))
			return $this->stream[$this->map[$name]]->value;
		else
			trigger_error("setVar: $name is not a defined variable", E_ERROR);
	}
	
	function getPacked()
	{
		$packed = '';
		//TODO: Dirty way of doing things. Fix later with bit operations
		$s = '';
		foreach($this->stream as $variable)
		{
			$tmp = decbin($variable['value']);
			$pad = '0';
			if ($variable['type'] == 'S' && $variable['value']<0)
				$pad = '1';
			$tmp = str_pad($tmp,$variable['length'],$pad,STR_PAD_LEFT);
			$s .= $tmp;
		}
		$sbytes  = str_split($s,8);
		foreach($sbytes as $sbyte)
		{
			$byte = bindec($sbyte);
			if (strlen($sbyte)<8)
			{
				$byte = $byte << (8-strlen($sbyte));
			}
			$packed .= pack('C',$byte);
		}
		
		return $packed;
		
	}
	
	
}

class RECT extends swftype
{
	var $bitstream;
	var $nbits;
	var $Xmin, $Xmax, $Ymin, $Ymax;
	
	function RECT($Xmin, $Xmax, $Ymin, $Ymax)
	{
		parent::swftype();
		$this->Xmin = $Xmin;
		$this->Xmax = $Xmax;
		$this->Ymin = $Ymin;
		$this->Ymax = $Ymax;
		$nbits = $this->calculateHighestBit();
		$this->bitstream=new BitStream(
				array(
					array('name'=>'nbits', 'length'=>5, 'type'=>'U', 'value'=>$nbits),
					array('name'=>'Xmin', 'length'=>$nbits, 'type'=>'S', 'value'=>$Xmin),
					array('name'=>'Xmax', 'length'=>$nbits, 'type'=>'S', 'value'=>$Xmax),
					array('name'=>'Ymin', 'length'=>$nbits, 'type'=>'S', 'value'=>$Ymin),
					array('name'=>'Ymax', 'length'=>$nbits, 'type'=>'S', 'value'=>$Ymax)
					)
			); 
		
	}
	
	function calculateHighestBit()
	{
		$all = $this->Xmin | $this->Xmax | $this->Ymin | $this->Ymax;
		for($i=31;$i>=0;$i--)
		{
			if ((pow(2,$i)&$all)!=0)
			{
				$i+=2;
				break;
			}
		}
		return $i;
			   
	}
	
	function getPacked()
	{
		return $this->bitstream->getPacked();		
	}
}

class ZeroString extends swftype
{
	function getPacked()
	{
		return $this->data . pack('C',0);
	}	
}

class UTF8String extends swftype
{
	var /* u30 */ $size;
	var /* u8[]*/ $utf8data;
	
	function UTF8String($string)
	{
		parent::swftype();
		$this->utf8data = $string;
		$this->size = new U30(strlen($this->utf8data));
		
	}
	
	function getPacked()
	{
		return $this->size->getPacked() . $this->utf8data;
	}
}


class RGB extends swftype
{
	var /* U8 */ $red;
	var /* U8 */ $green;
	var /* U8 */ $blue;
	function RGB($red, $green, $blue)
	{
		$this->red = new U8($red);
		$this->green = new U8($green);
		$this->blue = new U8($blue);
	}
	
	function getPacked()
	{
		return $this->red->getPacked() . $this->green->getPacked() . $this->blue->getPacked();
	}
}

?>
