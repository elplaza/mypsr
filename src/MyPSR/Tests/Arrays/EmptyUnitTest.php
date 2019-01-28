<?php

namespace MyPSR\Tests\Arrays;

class EmptyUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			5  => 1,
			7  => 1,
			9  => 1,
			11 => 1,
			14 => 1,
			18 => 1,
			22 => 1,
			27 => 1
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
