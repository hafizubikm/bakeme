<?php
require_once('../swx_config.php');

require_once('../SwxAssembler.php');

class SwxAssemblerTest extends UnitTestCase
{
	var $swxAssembler;
	
	function setUp()
	{
		$this->swxAssembler = new SwxAssembler();
	}

/*
	function testPush ()
	{
		//
		// Strings
		//
		
		// The string 'hello'
		$this->assertPushIsCorrect('hello', '960D000064617461000068656C6C6F00');
		
		// Empty string
		$this->assertPushIsCorrect('', '9608000064617461000000');
		
		// String longer than 255 characters (multi-byte length)
		// Note: Max length of a string is 65535 bytes.
		$data = '0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345';
		$this->assertPushIsCorrect($data, '960801006461746100003031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343500');
		
		// Array [1,2,3]
		$data = array(1, 2, 3);
		$this->assertPushIsCorrect($data, '961A00006461746100070300000007020000000701000000070300000042');
		
		// Boolean
		$data = true;
		$this->assertPushIsCorrect($data, '9608000064617461000501');
	}
*/	
	
	function testCreateSwf()
	{
		// Correct SWF bytecode that contains a single frame with the action data="hello" in it
		/*
		$correctSwfBytecode = '4657530631000000300A00A0000101004302FFFFFF3F0312000000960D000064617461000068656C6C6F001D0040000000';
		
		$testSwfBytecode = $this->swxAssembler->createSwf('hello');
		$this->assertEqual($correctSwfBytecode, $testSwfBytecode);
		*/
		
		// TODO: Add an actual test for this that passes an array. 
		
	}
	
	
	// Test generic data to bytecode 
	function testDataToBytecode()
	{
		// 'hello'
		$correctBytecode = '0068656C6C6F00';
		$str = 'hello';
		$this->assertEqual($correctBytecode, $this->swxAssembler->dataToBytecode($str));
		
		// [1, 2, 3]
		$correctBytecode = '961400070300000007020000000701000000070300000042';
		$testBytecode = $this->swxAssembler->dataToBytecode(array(1, 2, 3));
		$this->assertEqual($correctBytecode, $testBytecode);
		
		// null
		$this->assertEqual('02', $this->swxAssembler->dataToBytecode(null));
		
		// Double float (3.14)
		// $correctBytecode = '06 B8 1E 09 40 20 85 EB 51'
		
		// TODO: Add tests for object, etc. 
		
	}
	
	// Test string to bytecode conversion
	function testStringToBytecode()
	{
		// 'hello'
		$correctBytecode = '0068656C6C6F00';
		$str = 'hello';
		$this->assertEqual($correctBytecode, $this->swxAssembler->stringToBytecode($str));
		
		// Empty string
		$testBytecode = $this->swxAssembler->stringToBytecode('');
		$this->assertEqual('0000', $testBytecode);
		$this->assertEqual(4, strlen($testBytecode));
	}
	
	// Test boolean to bytecode conversion
	function testBooleanToBytecode()
	{
		// True test
		$this->assertEqual('0501', $this->swxAssembler->booleanToBytecode(true));
		
		// False test
		$this->assertEqual('0500', $this->swxAssembler->booleanToBytecode(false));		
	}
	
	// Test array to bytecode conversion
	function testArrayToBytecode()
	{
		// [1, 2, 3]
		$correctBytecode = '961400070300000007020000000701000000070300000042';
		
		$testBytecode = $this->swxAssembler->arrayToBytecode(array(1, 2, 3));
		$this->assertEqual($correctBytecode, $testBytecode);
		
		// error_log('SSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS');
		
		/*
		// data = [{x: 1, y: 2, z: 3}, {a: 4, b: 5, c: [true, false]}, [6, 7, {d: 8, e: 9}]];
		// $correctBytecode = '961B0000646174610000640007080000000065000709000000070200000043960F0007070000000706000000070300000042961C000061000704000000006200070500000000630005000501070200000042960500070300000043961D0000780007010000000079000702000000007A000703000000070300000043960500070300000042';
		$correctBytecode = '00640007080000000065000709000000070200000043960F0007070000000706000000070300000042961C000061000704000000006200070500000000630005000501070200000042960500070300000043961D0000780007010000000079000702000000007A000703000000070300000043960500070300000042';
		$testData = array(array('x' => 1, 'y' => 2, 'z' => 3), array('a' => 4, 'b' => 5, 'c' => array(true, false)), array(6, 7, array('d' => 8, 'e' => 9)));
		
		$this->assertEqual($correctBytecode, $this->swxAssembler->arrayToBytecode($testData));
*/
		//error_log('EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE');

		
		// [{x: 1, y: 2, z: 3}]
		/*
		$correctBytecode = '962500006461746100050100780007010000000079000702000000007A000703000000070300000043960500070200000042';
		
		$testBytecode = $this->swxAssembler->arrayToBytecode(array(array('x'=>1, 'y'=>2, 'z'=>3)));
		$this->assertEqual($correctBytecode, $testBytecode);
		*/
	}
	
	
	// Test integer to bytecode conversion
	function testIntegerToBytecode()
	{
		$intToTest = 16711680;
		$correctBytecode = '070000FF00';
		
		$this->assertEqual($correctBytecode, $this->swxAssembler->integerToBytecode($intToTest));
	}
	
	// Test double to bytecode conversion
	// Note: Not all float values generated by PHP and Flash will be 
	// entirely identical but, in testing, the slight difference in the 
	// hex representation isn't large enough to change the actual value. 
	// (This may be proven wrong with additional testing but I'm assuming
	// that it's a fact of life with floats.)
	function testDoubleToBytecode()
	{
		$correctBytecode = '06DD9ABFBF5F633937';
		$floatToTest = -0.123456789;
		
		$this->assertEqual($correctBytecode, $this->swxAssembler->doubleToBytecode($floatToTest));
		
	}
	
	
	function testGetIntAsHex()
	{
		$intToTest = 16711680;
		$numBytes = 8;
		$correctString = '0000FF0000000000';
		
		$this->assertEqual($correctString, $this->swxAssembler->getIntAsHex($intToTest, $numBytes));
	}
	
	

	function testGetStringLengthInBytesHex()
	{
		// Single-byte length
		$lengthInBytesHex = $this->swxAssembler->getStringLengthInBytesHex('68656c6c6f', 2);
		$this->assertEqual('0500', $lengthInBytesHex);

		// Double-byte length
		$lengthInBytesHex = $this->swxAssembler->getStringLengthInBytesHex('006461746100003031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343536373839303132333435363738393031323334353637383930313233343500', 2);	
		$this->assertEqual('0801', $lengthInBytesHex);
	}
	
	//
	// Helper method tests
	//

	function testMakeLittleEndian()
	{
		// Single byte
		$this->assertEqual('AB', $this->swxAssembler->makeLittleEndian('AB'));
		
		// Two bytes
		$this->assertEqual('BBAA', $this->swxAssembler->makeLittleEndian('AABB'));	
	}
	
	function testStrhex()
	{
		$this->assertEqual('68656c6c6f', $this->swxAssembler->strhex('hello'));
	}
	
	function testHexStr()
	{
		$this->assertEqual('hello', $this->swxAssembler->hexstr('68656c6c6f'));
	}
	
	//
	// Helper methods
	//
	
	function assertPushIsCorrect($data, $correctBytecode)
	{
		// Create the bytecode for the push statement 
		// based on the passed $data.
		$result = $this->swxAssembler->push('data', $data);
		
		$this->assertEqual(strlen($result), strlen($correctBytecode));
		$this->assertEqual($result, $correctBytecode);		
	}
}

?>