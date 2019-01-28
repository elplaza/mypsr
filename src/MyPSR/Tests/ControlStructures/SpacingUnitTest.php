<?php

namespace MyPSR\Tests\ControlStructures;

class SpacingUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			3  => 2,
			4  => 2,
			7  => 2,
			9  => 4,
			11 => 1,
			14 => 1,
			19 => 1,
			21 => 2,
			23 => 2,
			25 => 3,
			27 => 2
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
