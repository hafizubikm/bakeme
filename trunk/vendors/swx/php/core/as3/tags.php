<?php
define("END_TAG_TYPE",0);
define("FILEATTRIBUTES_TAG_TYPE",69);
define("DOABC_TAG_TYPE",82);
define("SYMBOLCLASS_TAG_TYPE",76);
define("SHOWFRAME_TAG_TYPE",1);
define("SETBACKGROUNDCOLOR_TAG_TYPE",9);

class FileAttributesTag extends Tag
{
	var /*BitStream*/ $bitstream;
	
	function FileAttributesTag()
	{
		parent::Tag();
		$this->setTagType(FILEATTRIBUTES_TAG_TYPE);
		$this->bitstream=new BitStream(
				array(
					array('name'=>'Reserved', 'length'=>3, 'type'=>'U', 'value'=>0),
					array('name'=>'HasMetaData', 'length'=>1, 'type'=>'U', 'value'=>0),
					array('name'=>'ActionScript3', 'length'=>1, 'type'=>'U', 'value'=>1),
					array('name'=>'Reserved', 'length'=>2, 'type'=>'U', 'value'=>0),
					array('name'=>'UseNetwork', 'length'=>1, 'type'=>'U', 'value'=>0),
					array('name'=>'Reserved', 'length'=>24, 'type'=>'U', 'value'=>0)
					)
			); 
	}
	
	function getPacked()
	{
		return $this->bitstream->getPacked();
	}
	
	function dump()
	{
		parent::dump();
		echo "<pre>".print_r($this,true)."</pre>";
		
	}
	
}

class EndTag extends Tag
{
	function EndTag()
	{
		parent::Tag();
		$this->setTagTYpe(END_TAG_TYPE);
	}
	
	function getPacked()
	{
		return '';
	}
	
}

define('kDoAbcLazyInitializeFlag',1);
class DoABC extends Tag
{
	var /*U32*/ $Flags;
	
	var /*String*/ $Name;

	var /*abcFile*/ $abcFile;
	
	function DoABC($flags=0)
	{
		parent::Tag();
		$this->setTagTYpe(DOABC_TAG_TYPE);
		$this->Flags = new U32($flags);
		$this->Name = new ZeroString();
		$this->abcFile = new abcFile();
		
	}
	
	function getPacked()
	{
		return $this->Flags->getPacked() . $this->Name->getPacked() . $this->abcFile->getPacked();
	}
	
	function dump()
	{
		parent::dump();
		echo "<pre>".print_r($this,true)."</pre>";
	}
	
	function printStats()
	{
		echo "ABC Tag stats:<br/>";
		echo "Tag length: ";
		if ($this->Length != null)
			echo $this->Length->get();
		else
			echo $this->TagCodeAndLength->get() & 63;
		echo "<br/>";
		$this->abcFile->constant_pool->printStats();
	}
}

class SymbolClass extends Tag
{
	var /* U16 */ $NumSymbols;
	var $data;
	
	function SymbolClass()
	{
		parent::Tag();
		$this->setTagTYpe(SYMBOLCLASS_TAG_TYPE);
		$this->NumSymbols = new U16(0);
		$this->data = new abcArray();
		
	}
	
	function addSymbol($TagID,$Name)
	{
		$this->data->add(new U16($TagID));
		$this->data->add(new ZeroString($Name));
		$this->NumSymbols->set($this->NumSymbols->get()+1);
	}
	
	function getPacked()
	{
		return $this->NumSymbols->getPacked() . $this->data->getPacked(); 
	}
	
	function getTagContent()
	{
		/*
		 * Dev not: I'm not sure why but flash seems to use the length of the SymbolClass tag differently than the others.
		 * It appears the long record header is used no matter what. (Normaly only if length > 63 (0x3f)). 
		 * Keeping it like so in case it's a compatibility issue.
		 */
		
		$packed = $this->getPacked();
		$length = strlen($packed);

		$this->TagCodeAndLength->set($this->TagCodeAndLength->get() | 0x3f) ;
		$this->Length = new U32($length);

		return $this->TagCodeAndLength->getPacked() . $this->Length->getPacked() . $packed;
				
	}
	
	function dump()
	{
		parent::dump();
		echo "<pre>".print_r($this,true)."</pre>";
	}
	
}

class ShowFrame extends Tag
{
	function ShowFrame()
	{
		parent::Tag();
		$this->setTagTYpe(SHOWFRAME_TAG_TYPE);
	}
	
	function getPacked()
	{
		return '';
	}
}


class SetBackgroundColor extends Tag
{
	var /* RGB */ $rgb;
	
	function SetBackgroundColor($red, $green, $blue)
	{
		parent::Tag();
		$this->setTagTYpe(SETBACKGROUNDCOLOR_TAG_TYPE);
		$this->rgb = new RGB($red,$green, $blue);
	}

	function getPacked()
	{
		return $this->rgb->getPacked();	
	}
}

class TheMisteryTag extends Tag		 // is it DefineSceneAndFrameLabelData? Not usefull for the final result anyway
{
	function TheMisteryTag()
	{
		
	}
	
	function getTagContent()
	{
		$data = array(0xBF,0x15,0x0B,0x00,0x00,0x00,0x01,0x00,0x53,0x63,0x65,0x6E,0x65,0x20,0x31,0x00,0x00);
		$s = '';
		foreach($data as $byte)
		{
			$s .= pack( 'C', $byte );
		}
		return $s;
		
	}
	
	function dump()
	{
		echo "<hr/>Tag ID Mystery Tag";
		//$content = $this->getTagContent();
		 
		
		echo "<br/>";
		
	}
}

?>