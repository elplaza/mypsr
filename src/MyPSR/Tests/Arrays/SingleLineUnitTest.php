<?php

namespace MyPSR\Tests\Arrays;

class SingleLineUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			9  => 1,
			11 => 1,
			13 => 2,
			15 => 2,
			27 => 2,
			29 => 2,
			31 => 1,
			33 => 2,
			35 => 4,
			37 => 2,
			39 => 2,
			41 => 4,
			43 => 3,
			45 => 2,
			47 => 4,
			49 => 1,
			51 => 14
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
