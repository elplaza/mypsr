<?php

namespace MyPSR\Tests\WhiteSpace;

class MultilineUnitTest extends \MyPSR\Tests\AbstractSniffTest
{

	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @return array<int, int>
	 */
	public function getErrorList()
	{
		return array(
			6  => 1,
			7  => 1,
			11 => 2,
			12 => 2,
			18 => 1,
			20 => 1,
			26 => 1,
			29 => 1,
			31 => 1,
			32 => 1,
			38 => 1,
			42 => 1,
			44 => 2,
			48 => 1,
			49 => 1,
			50 => 1,
			56 => 1,
			59 => 1
		);

	}//end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @return array<int, int>
	 */
	public function getWarningList()
	{
		return array();

	}//end getWarningList()

}//end class
