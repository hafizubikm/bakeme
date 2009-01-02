<?php
include_once('core/as3/datatypes.php');
include_once('core/as3/swfHeader.php');
include_once('core/as3/tag.php');
include_once('core/as3/tags.php');
include_once('core/as3/method_info.php');
include_once('core/as3/class_info.php');
include_once('core/as3/script_info.php');
include_once('core/as3/instance_info.php');
include_once('core/as3/method_body_info.php');
include_once('core/as3/avm2_instructions.php');
include_once('core/as3/traits_info.php');
include_once('core/as3/namespace.php');
include_once('core/as3/multiname.php');
include_once('core/as3/abcFile.php');
include_once('core/as3/asmUtility.php');
include_once('core/as3/constantPool.php');
include_once("core/as3/abcArray.php");

// PHP 5 compatibility layer for PHP 4
require_once('lib/str_split.php');


class SwxAssembler3
{
	var $header;
	var $tags;

	var $datastack;
	var $stackcounter = 0;
	var $maxstack = 0;

	var $data = '';
	
	var $currentABC = null;
	
	var $utility;
	
	var $url = '';
	
	function SwxAssembler3()
	{
		$this->header = new swfHeader();
		$this->tags = array();
		$this->tags[] = new FileAttributesTag();
		$this->datastack = array();
		$this->utility = new asmUtility();
	}

	function dump()
	{
		echo "Dumping:<br/>";
		echo $this->header->dump();
		foreach($this->tags as $tag)
			$tag->dump();
	}

	function compile($compressionLevel=0)
	{
		$this->header->setCompressed($compressionLevel>0);
		
		$data = '';
		foreach($this->tags as $tag)
			$data .= $tag->getTagContent();
		
		$header = $this->header->getPacked();
		$len = strlen($header)+strlen($data);
		$this->header->filelength->set($len);
		
		$data = $this->header->getPacked() . $data;

		if ($compressionLevel > 0)
		{
			
			
			$compressionStartTime = $this->microtime_float();
			
			// The first eight bytes are uncompressed
			$uncompressedBytes = substr($data, 0, 8);
			
			// Remove first eight bytes
			$data = substr_replace($data, '', 0, 8);
			
			// Compress the rest of the SWF
			$data = gzcompress($data, $compressionLevel);
			
			// Add the uncompressed header
			$data = $uncompressedBytes . $data;
			
			$compressionDuration = $this->microtime_float() - $compressionStartTime;
			if (LOG_ALL) error_log('[SWX] PROFILING: SWF compression took ' . $compressionDuration . ' seconds.'); 
			
			// Stats
			$compressedSize = strlen($data);
			if (LOG_ALL) error_log('[SWX] INFO Compressed size of SWF: ' . $compressedSize . ' bytes.'); 
		}
		

		$this->data = $data;
	}
	
	function writeToFile($file)
	{
		
		$fp = fopen($file, 'w');
		fwrite($fp, $this->data);
		fclose($fp);
	}

	function hexdump ($data = null, $htmloutput = true, $uppercase = false, $return = false)
	{
		if ($data==null)
			$data = $this->data;
	    // Init
	    $hexi   = '';
	    $ascii  = '';
	    $dump   = ($htmloutput === true) ? '<pre>' : '';
	    $offset = 0;
	    $len    = strlen($data);

	    // Upper or lower case hexidecimal
	    $x = ($uppercase === false) ? 'x' : 'X';

	    // Iterate string
	    for ($i = $j = 0; $i < $len; $i++)
	    {
	        // Convert to hexidecimal
	        $hexi .= sprintf("%02$x ", ord($data[$i]));

	        // Replace non-viewable bytes with '.'
	        if (ord($data[$i]) >= 32) {
	            $ascii .= ($htmloutput === true) ?
	                            htmlentities($data[$i]) :
	                            $data[$i];
	        } else {
	            $ascii .= '.';
	        }

	        // Add extra column spacing
	        if ($j === 7) {
	            $hexi  .= ' ';
	            $ascii .= ' ';
	        }

	        // Add row
	        if (++$j === 16 || $i === $len - 1) {
	            // Join the hexi / ascii output
	            $dump .= sprintf("%04$x  %-49s  %s", $offset, $hexi, $ascii);

	            // Reset vars
	            $hexi   = $ascii = '';
	            $offset += 16;
	            $j      = 0;

	            // Add newline
	            if ($i !== $len - 1) {
	                $dump .= "\n";
	            }
	        }
	    }

	    // Finish dump
	    $dump .= $htmloutput === true ?
	                '</pre>' :
	                '';
	    $dump .= "\n";

	    // Output method
	    if ($return === false) {
	        echo $dump;
	    } else {
	        return $dump;
	    }
	}

	function writeSwf($data, $debug = false, $compressionLevel = 4, $url = '')
	{
			
		$this->url = $url;
		$this->prepareContent($data, 'result', $debug);
		$this->compile($compressionLevel);
		
		
			
		header("Content-Type: application/swf;");
		header('Content-Disposition: inline; filename="data.swf"');
		header('Content-Length: ' . strlen($this->data));
		echo $this->data;
	}
	
	function prepareContent(&$data, $varname = 'result', $debug=false)
	{
		
		$abc = new DoAbc(kDoAbcLazyInitializeFlag);
		$abcFile = &$abc->abcFile;
		$this->currentABC = &$abc;
		
		$stringpool = &$abcFile->constant_pool->string;
		
		$stringpool->add(new UTF8String(''),'_blank_');
		$stringpool->add(new UTF8String('swxResponse'),'swxResponse');
		$stringpool->add(new UTF8String('flash.display'),'flash.display');
		$stringpool->add(new UTF8String('MovieClip'),'MovieClip');
		$stringpool->add(new UTF8String("$varname"),"$varname");
		$stringpool->add(new UTF8String('Object'),'Object');
		$stringpool->add(new UTF8String('flash.events'),'flash.events');
		$stringpool->add(new UTF8String('EventDispatcher'),'EventDispatcher');
		$stringpool->add(new UTF8String('DisplayObject'),'DisplayObject');
		$stringpool->add(new UTF8String('InteractiveObject'),'InteractiveObject');
		$stringpool->add(new UTF8String('DisplayObjectContainer'),'DisplayObjectContainer');
		$stringpool->add(new UTF8String('Sprite'),'Sprite');
		
		
		$namespacepool = &$abcFile->constant_pool->namespace;
		$namespacepool->add(new namespace_info(CONSTANT_PackageNamespace,$stringpool->getIndexOf('_blank_')),'*');
		$namespacepool->add(new namespace_info(CONSTANT_PackageNamespace,$stringpool->getIndexOf('flash.display')),'flash.display');
		$namespacepool->add(new namespace_info(CONSTANT_ProtectedNamespace,$stringpool->getIndexOf('swxResponse')),'swxResponse');
		$namespacepool->add(new namespace_info(CONSTANT_PackageInternalNs,$stringpool->getIndexOf('_blank_')),'_blank_');
		$namespacepool->add(new namespace_info(CONSTANT_PackageNamespace,$stringpool->getIndexOf('flash.events')),'flash.events');
		
		
		$multinamepool = &$abcFile->constant_pool->multiname;
		$multinamepool->addQname('*','swxResponse',$abcFile->constant_pool);
		$multinamepool->addQname('flash.display','MovieClip',$abcFile->constant_pool);
		$multinamepool->addQname('*',"$varname",$abcFile->constant_pool);
		$multinamepool->addQname('*','Object',$abcFile->constant_pool);
		$multinamepool->addQname('flash.events','EventDispatcher',$abcFile->constant_pool);
		$multinamepool->addQname('flash.display','DisplayObject',$abcFile->constant_pool);
		$multinamepool->addQname('flash.display','InteractiveObject',$abcFile->constant_pool);
		$multinamepool->addQname('flash.display','DisplayObjectContainer',$abcFile->constant_pool);
		$multinamepool->addQname('flash.display','Sprite',$abcFile->constant_pool);
		
		$method = &$abcFile->method;
		$method->add(new method_info(CONSTANT_AnonymousMethod, CONSTANT_ReturnAny, array(), NO_FLAG),'staticinitializer');
		$method->add(new method_info(CONSTANT_AnonymousMethod, CONSTANT_ReturnAny, array(), NO_FLAG),'constructor');
		$method->add(new method_info(CONSTANT_AnonymousMethod, CONSTANT_ReturnAny, array(), NO_FLAG),'scriptinit');
		
		$instance = &$abcFile->instance;
		
		$instance->add( new instance_info(
				$multinamepool->getIndexOf('*:swxResponse'),
				$multinamepool->getIndexOf('flash.display:MovieClip'),
				CONSTANT_ClassProtectedNs | CONSTANT_ClassSealed,
				$namespacepool->getIndexOf('swxResponse'),
				array(),
				$method->getIndexOf('constructor'),
				array(
					$this->utility->makeSlot($multinamepool->getIndexOf("*:$varname"),0,0,0,0)
				)
			));
		
		$class = &$abcFile->class;
		
		$class->add (new class_info(
			$method->getIndexOf('staticinitializer'),
			array()
		),'swxResponse');
		
		
		$script = &$abcFile->script;
		
		$script->add (new script_info(
			$method->getIndexOf('scriptinit'),
			array(
				$this->utility->makeClass($multinamepool->getIndexOf('*:swxResponse'),/*slot_id*/1,$class->getIndexOf('swxResponse'))
			)
		));
		
		
		
		$this->mainABCFile = &$abcFile;
	
		
		
		$this->createDataOperations($data);
		
		if ($this->url!='')
		{
			
			$stringpool->add(new UTF8String('flash.system'),'flash.system');
			$stringpool->add(new UTF8String('Security'),'Security');
			$stringpool->add(new UTF8String($this->url),$this->url);
			$stringpool->add(new UTF8String('allowDomain'),'allowDomain');
			
			$namespacepool->add(new namespace_info(CONSTANT_PackageNamespace,$stringpool->getIndexOf('flash.system')),'flash.system');
			$multinamepool->addQname('flash.system','Security',$abcFile->constant_pool);
			$multinamepool->addQname('*','allowDomain',$abcFile->constant_pool);
			
			$instructions = array(
					new instruction(op_getlocal0),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.system:Security')),
					new instruction(op_pushstring, new U30($stringpool->getIndexOf($this->url))),
					$this->utility->callpropvoid($multinamepool->getIndexOf('*:allowDomain'),1),
					new instruction(op_returnvoid)
					);
			$max_stack = 2;
		}
		else
		{
			$instructions = array(
					new instruction(op_getlocal0),
					new instruction(op_pushscope),
					new instruction(op_returnvoid)
					);
			$max_stack = 1;
		}
		
		$method_body = &$abcFile->method_body;
		$method_body->add(
			new method_body_info( /* $method, $max_stack, $local_count, $init_scope_depth, $max_scope_depth, $code, $exceptions, $traits */
				$method->getIndexOf('staticinitializer'),$max_stack,1,9,10,
				$instructions,
				array(),
				array()
			)
		);
		
		if ($debug)
		{
			$stringpool->add(new UTF8String('flash.net'),'flash.net');
			$stringpool->add(new UTF8String('LocalConnection'),'LocalConnection');
			$stringpool->add(new UTF8String('debug'),'debug');
			$stringpool->add(new UTF8String('send'),'send');
			$stringpool->add(new UTF8String('x'),'x');  // Wrap root object in another for the analyzer
			$stringpool->add(new UTF8String('_swxDebugger'),'_swxDebugger');

			$namespacepool->add(new namespace_info(CONSTANT_PackageNamespace,$stringpool->getIndexOf('flash.net')),'flash.net');

			$multinamepool->addQname('flash.net','LocalConnection',$abcFile->constant_pool);
			$multinamepool->addQname('*','send',$abcFile->constant_pool);
						
			$constructor_maxstack_offset = 4;
			$constructor_local_count = 2;
		}
		else
		{
			// No debug
			$constructor_maxstack_offset = 2;
			$constructor_local_count = 1;
		}
		
		
		$method_body->add(
			new method_body_info(
				//$method->getIndexOf('constructor'),$this->maxstack+2,1,10,11,
				$method->getIndexOf('constructor'),$this->maxstack+$constructor_maxstack_offset,$constructor_local_count,10,11,
				array_merge(
					array(
							new instruction(op_getlocal0),
							new instruction(op_pushscope),
							$this->utility->findproperty($multinamepool->getIndexOf("*:$varname")),
							),
							$this->datastack,
							
							array_merge(
								array(
									$this->utility->initproperty($multinamepool->getIndexOf("*:$varname")),
									new instruction(op_getlocal0),
									$this->utility->constructsuper(0),
								),
								
								$debug ? array(
									// Debug is on: write the LocalConnection call.
									$this->utility->findpropstrict($multinamepool->getIndexOf('flash.net:LocalConnection')),
									$this->utility->constructprop($multinamepool->getIndexOf('flash.net:LocalConnection'), 0),
									$this->utility->coerce($multinamepool->getIndexOf('flash.net:LocalConnection')),
									new instruction(op_setlocal1),
									new instruction(op_getlocal1),
									new instruction(op_pushstring, new U30($stringpool->getIndexOf('_swxDebugger'))),
									new instruction(op_pushstring, new U30($stringpool->getIndexOf('debug'))),
								
									// Wrap the result in an object -- this is what the analyzer expects 
									// (it fails on simple datatypes). Calling the attribute x for brevity 
									// as it doesn't matter what it's called.
									new instruction(op_pushstring, new U30($stringpool->getIndexOf('x'))),
									$this->utility->getlex($multinamepool->getIndexOf("*:$varname")),
									$this->utility->newobject(1),
								
									$this->utility->callpropvoid($multinamepool->getIndexOf('*:send'),3),								
	
								): array(),
								
								array(
									new instruction(op_returnvoid)
								)
							)
						),
				array(),
				array()
			)
		);
		
		
		
		$method_body->add(
			new method_body_info( 
				$method->getIndexOf('scriptinit'),2,1,1,9,
				array(
					new instruction(op_getlocal0),
					new instruction(op_pushscope),
					new instruction(op_getscopeobject,new U8(0)),
					$this->utility->getlex($multinamepool->getIndexOf('*:Object')),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.events:EventDispatcher')),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.display:DisplayObject')),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.display:InteractiveObject')),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.display:DisplayObjectContainer')),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.display:Sprite')),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.display:MovieClip')),
					new instruction(op_pushscope),
					$this->utility->getlex($multinamepool->getIndexOf('flash.display:MovieClip')),
					$this->utility->newclass($class->getIndexOf('swxResponse')),
		
					new instruction(op_popscope),
					new instruction(op_popscope),
					new instruction(op_popscope),
					new instruction(op_popscope),
					new instruction(op_popscope),
					new instruction(op_popscope),
					new instruction(op_popscope),
		
					$this->utility->initproperty($multinamepool->getIndexOf('*:swxResponse')),
					new instruction(op_returnvoid)
				),
				array(),
				array()
			)
		);
		
		
		$this->tags[] = $abc;
		
		/* this tag seems necessary for flash to start interpreting the content. without it no error, but no 'result' */
		$symbolclass = new SymbolClass();
		$symbolclass->addSymbol(0,"swxResponse");
		$this->tags[] = $symbolclass;
		
		
		
		
		$this->tags[] = new ShowFrame();
		$this->tags[] = new EndTag();
		
	}
	
	function createDataOperations(&$data)
	{
		if ($data===NULL)
			$this->createDataOperations_NULL();
		elseif (is_integer($data))
			$this->createDataOperations_integer($data);
		elseif (is_string($data))
			$this->createDataOperations_string($data);
		elseif (is_bool($data))
			$this->createDataOperations_boolean($data);
		elseif (is_double($data))
			$this->createDataOperations_double($data);
		elseif (is_array($data))
			$this->createDataOperations_array($data);
		elseif (is_object($data))
			$this->createDataOperations_object($data);
		else
			trigger_error('Unhandled data type ('.gettype($data).')', E_USER_ERROR);

	}

	function createDataOperations_integer(&$data)
	{
		if ($data <= 255 && $data >=0)
		{
			$this->stackCounterPlus();
			$this->datastack[]= new instruction(op_pushbyte,  new U8($data));
		}
		else
		{
			$index = $this->mainABCFile->constant_pool->integer->getIndexOf($data);

			if ($index === FALSE)
			{
				$index = $this->mainABCFile->constant_pool->integer->add(new S32($data),$data);

			}
			$this->stackCounterPlus();
			$this->datastack[]= new instruction(op_pushint,  new U30($index));
		}
	}
	
	function createDataOperations_boolean(&$data)
	{
		$this->stackCounterPlus();
		if ($data===TRUE)
			$this->datastack[]= new instruction(op_pushtrue);
		else
			$this->datastack[]= new instruction(op_pushfalse);
	}
	
	function createDataOperations_NULL()
	{
		$this->stackCounterPlus();
		$this->datastack[]= new instruction(op_pushnull);
	}
	
	function createDataOperations_double(&$data)
	{
		$this->stackCounterPlus();
		$index = $this->mainABCFile->constant_pool->double->getIndexOf('dbl_'.$data);

		if ($index === FALSE)
		{
			$index = $this->mainABCFile->constant_pool->double->add(new D64($data),'dbl_'.$data);

		}
		$this->stackCounterPlus();
		$this->datastack[]= new instruction(op_pushdouble,  new U30($index));
	}

	function createDataOperations_string(&$data)
	{
		$index = $this->mainABCFile->constant_pool->string->getIndexOf($data);
		if ($index === FALSE)
		{
			$index = $this->mainABCFile->constant_pool->string->add(new UTF8String($data),$data);
		}
		$this->stackCounterPlus();
		$this->datastack[]= new instruction(op_pushstring, new U30($index));
	}

	function createDataOperations_array(&$data)
	{
		$arrCount = count($data);

		// Determine array type
		$keys = array_keys($data);

		if ($arrCount == 0)
		{
			$this->stackCounterPlus(1);	// the empty array's pointer will be pushed into the stack
			$this->datastack[] = new instruction(op_newarray,new U30(0));
		}
		elseif (is_integer($keys[0]) == 'integer')
		{
			foreach($data as $item)
			{
				$this->createDataOperations($item);

			}
			$this->stackCounterMinus($arrCount-1); // all elements popped, array pointer pushed
			$this->datastack[] = new instruction(op_newarray,new U30($arrCount));

		}
		else
		{
			$this->createDataOperations_object($data);
		}
	}

	function createDataOperations_object(&$data)
	{
		$props_count = 0;

		foreach($data as $key => $value)
		{
			$keyindex = $this->mainABCFile->constant_pool->string->getIndexOf($key);
			if ($keyindex === FALSE)
			{
				$keyindex = $this->mainABCFile->constant_pool->string->add(new UTF8String($key),$key);
			}
			$this->stackCounterPlus();
			$this->datastack[] = new instruction(op_pushstring, new U30($keyindex));

			$this->createDataOperations($value);
			$props_count++;

		}
		
		$this->stackCounterMinus(($props_count*2)-1);	// props_count x 2 (1 property/value pair) popped, object pointer pushed
		$this->datastack[] = new instruction(op_newobject,new U30($props_count));

	}

	/** Count maximum amount of entries ever add on the stack when building operations list to rebuild result **/ 
	function stackCounterPlus()
	{
		$this->stackcounter++;
		if ($this->stackcounter>$this->maxstack)
			$this->maxstack = $this->stackcounter;
	}
	function stackCounterMinus($amount=1)
	{
		$this->stackcounter-=$amount;
	}
	function stackCounterClear()
	{
		$this->stackcounter = 0;
	}

	function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	
	
	
	
}

?>
