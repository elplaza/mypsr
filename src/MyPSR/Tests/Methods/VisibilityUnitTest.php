<?php

namespace MyPSR\Tests\Methods;

class VisibilityUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			12 => 1,
			35 => 1,
			40 => 1,
			45 => 1,
			50 => 1,
			61 => 1,
			77 => 1
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
