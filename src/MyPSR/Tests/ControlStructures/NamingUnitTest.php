<?php

namespace MyPSR\Tests\ControlStructures;

class NamingUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			3  => 1,
			4  => 1,
			7  => 1,
			9  => 2,
			10 => 1,
			13 => 1
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
