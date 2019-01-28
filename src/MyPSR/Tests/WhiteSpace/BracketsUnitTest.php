<?php

namespace MyPSR\Tests\WhiteSpace;

class BracketsUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			106 => 2,
			110 => 1,
			114 => 1,
			118 => 1,
			119 => 1,
			123 => 1,
			128 => 1,
			133 => 1,
			152 => 1,
			154 => 2,
			155 => 1
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
