<?php

namespace MyPSR\Tests\WhiteSpace;

class IndentationUnitTest extends \MyPSR\Tests\AbstractSniffTest
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
			4  => 1,
			8  => 1,
			11 => 1,
			12 => 1,
			15 => 1,
			16 => 1,
			22 => 1,
			23 => 1,
			24 => 1,
			26 => 1,
			34 => 1,
			35 => 1,
			40 => 1,
			41 => 1,
			46 => 1,
			47 => 1,
			48 => 1,
			49 => 1,
			51 => 1,
			52 => 1,
			55 => 1,
			59 => 1,
			61 => 1
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
