<?php
/*
 * Author: Richard Lord
 * Copyright (c) Big Room Ventures Ltd. 2007
 * Version: 1.0.0
 * 
 * Licence Agreement
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Functions to convert from PHP native numbers to flash player data types as
 * defined for the AVM2 / Actionscript 3 player.
 * 
 * All returned values are in little-endian byte order.
 * 
 * All methods return null if the value passed in is too large or small for 
 * conversion to the requested data type.
 */

/**
 * @param $n an integer between 0 and 255
 * @return an AVM2 U8 data type
 */
function AVM2_toU8( $n )
{
	if( $n != (integer) $n )
	{
		return null;
	}
	if( $n < 0 )
	{
		return null;
	}
	if( $n > 0xFF )
	{
		return null;
	}
	return pack( 'C', $n );
}

/**
 * @param $n an integer between 0 and 65,535
 * @return an AVM2 U16 data type
 */
function AVM2_toU16( $n )
{
	if( $n != (integer) $n )
	{
		return null;
	}
	if( $n < 0 )
	{
		return null;
	}
	if( $n > 0xFFFF )
	{
		return null;
	}
	return pack( 'v', $n );
}

/**
 * @param $n an integer between -8,388,608 and 8,388,607
 * @return an AVM2 S24 data type
 */
function AVM2_toS24( $n )
{
	if( $n != (integer) $n )
	{
		return null;
	}
	if( $n < -0x800000 )
	{
		return null;
	}
	if( $n > 0x7FFFFF )
	{
		return null;
	}
	if( $n < 0 )
	{
		$n = ( (-$n) ^ 0xFFFFFF ) + 1;
	}
	return pack( 'C*', $n & 0xFF, ( $n >> 8 ) & 0xFF, ( $n >> 16 ) & 0xFF );
}

/**
 * Used in other methods to create AVM2 variable length data types.
 */
function variableLength( $n )
{
	
	$out = '';
	if( $n > 0x7FFFFFFF )
	{
		$sign = true;
		$n &= 0x7FFFFFFF;
	}
	else
	{
		$sign = false;
	}
	do
	{
		$ret = $n & 0x7f;
		$n >>= 7;
		if( $sign )
		{
			$n |= 0x1000000;
			$sign = false;
		}
		if( $n )
		{
			$ret |= 0x80;
		}
		$out .= pack( 'C', $ret );
	}
	while( $n );
	return $out;
}

/**
 * @param $n an integer between 0 and 4,294,967,295
 * @return an AVM2 variable length U32 data type
 */
function AVM2_toU32( $n )
{
	if( $n != round( $n ) )
	{
		return null;
	}
	if( $n < 0 )
	{
		return null;
	}
	if( $n > 0xFFFFFFFF )
	{
		return null;
	}
	
	return variableLength( $n );
}

/**
 * @param $n an integer between 0 and 1,073,741,823
 * @return an AVM2 variable length U30 data type
 */
function AVM2_toU30( $n )
{
	if( $n != (integer) $n )
	{
		return null;
	}
	if( $n < 0 )
	{
		return null;
	}
	if( $n > 0x3FFFFFFF )
	{
		return null;
	}
	return variableLength( $n );
}

/**
 * @param $n an integer between -2,147,483,648 and 2,147,483,647
 * @return an AVM2 variable length U30 data type
 */
function AVM2_toS32( $n )
{
	if( $n != (integer) $n )
	{
		return null;
	}
	if( $n > 0x7FFFFFFF )
	{
		return null;
	}
	if( $n < -0x80000000 )
	{
		return null;
	}
	if( $n >= 0 )
	{
		return variableLength( $n );
	}
	else
	{
		$n = -$n;
		$c = 0x40;
		while( $n > $c )
		{
			$c <<= 7;
		}
		$c <<= 1;
		$c--;
		$n = ( $n ^ $c ) + 1;

		return variableLength( $n );
	}
}

/**
 * To 64bit float IEEE 754-2985
 */
function AVM2_toD64( $n )
{
	$chars = AVM2_toD64Array( $n );
	return pack( 'C*', $chars[0], $chars[1], $chars[2], $chars[3], $chars[4], $chars[5], $chars[6], $chars[7] );
}
function AVM2_toD64Array( $n )
{
	if( is_nan( (double) $n ) )
	{
		return array( 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF );
	}
	$chars = array( 0,0,0,0,0,0,0,0 );
	if( $n == 0 )
	{
		return $chars;
	}
	$i = 63;
	if( $n < 0 ) // handle sign
	{
		$chars[ $i >> 3 ] |= 1 << ( $i & 7 );
		$n = -$n;
	}
	--$i;
	$exp = 0;
	if( $n >= 2 )
	{
		while( $n >= 2 && $exp < 1024 )
		{
			$n /= 2;
			++$exp;
		}
	}
	else
	{
		while( $n < 1 && $exp > -1022 )
		{
			$n *= 2;
			--$exp;
		}
	}
	$exp += 1023; // the bias
	if( $exp == 1 && $n < 1 )
	{
		// denormalised
		$exp = 0;
	}
	for( $mask = 1024; $mask; $mask >>= 1, --$i )
	{
		if( $exp & $mask )
		{
			$chars[ $i >> 3 ] |= 1 << ( $i & 7 );
		}
	}
	if( $exp == 2047 )
	{
		// +/- infinity
		return $chars;
	}
	if( $n >= 1 )
	{
		// normalised
		$n -= 1;
	}
	while( $i >= 0 )
	{
		$n *= 2;
		if( $n >= 1 )
		{
			$chars[ $i >> 3 ] |= 1 << ( $i & 7 );
			$n -= 1;
		}
		--$i;
	}
	return $chars;
}