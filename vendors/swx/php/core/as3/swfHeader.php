<?php

class swfHeader extends swftype
{
	var $signature = 'FWS';
	var $version = 9;
	var /*UI32*/ $filelength;
	var /*RECT*/ $framesize;
	var /*UI16*/ $framerate ;
	var /*UI16*/ $framecount;

	function swfHeader($framerate = 12, $framecount = 1, $xmin = 0, $xmax = 1, $ymin=0, $ymax = 1)
	{
		parent::swftype();
		$this->filelength = new U32(0);
		$this->framesize = new RECT($xmin, $xmax, $ymin, $ymax); 
		$this->framerate = new U16($framerate<<8);
		$this->framecount = new U16($framecount);
		
	}
	
	function setCompressed ($bCompressed)
	{
		$this->signature= $bCompressed ? 'CWS' : 'FWS';
	}

	function getPacked()
	{
		$packed = $this->signature . pack('C',$this->version)  . $this->filelength->getPacked() ./* '['.*/$this->framesize->getPacked() /*.']'*/. $this->framerate->getPacked() . $this->framecount->getPacked();
		return $packed;
		
	}
	
	
}
?>