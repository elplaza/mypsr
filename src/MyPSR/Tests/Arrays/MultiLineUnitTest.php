<?php

namespace MyPSR\Tests\Arrays;

class MultiLineUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			9   => 1,
			13  => 1,
			17  => 1,
			35  => 1,
			39  => 1,
			41  => 2,
			46  => 1,
			63  => 1,
			64  => 1,
			65  => 1,
			70  => 1,
			71  => 1,
			78  => 1,
			80  => 2,
			84  => 2,
			88  => 1,
			94  => 2,
			95  => 2,
			97  => 2,
			98  => 2,
			101 => 2,
			103 => 5,
			104 => 4,
			106 => 2,
			107 => 6
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
