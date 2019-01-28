<?php

namespace MyPSR\Tests;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

abstract class AbstractSniffTest extends AbstractSniffUnitTest
{

	public static function setUpBeforeClass()
	{
		if (defined("PHP_CODESNIFFER_IN_TESTS") === false) {
			define("PHP_CODESNIFFER_IN_TESTS", true);
		}

		if (defined("PHP_CODESNIFFER_CBF") === false) {
			define("PHP_CODESNIFFER_CBF", false);
		}

		if (defined("PHP_CODESNIFFER_VERBOSITY") === false) {
			define("PHP_CODESNIFFER_VERBOSITY", 0);
		}

		$GLOBALS["PHP_CODESNIFFER_SNIFF_CODES"]   = array();
		$GLOBALS["PHP_CODESNIFFER_FIXABLE_CODES"] = array();

		if (!isset($GLOBALS["PHP_CODESNIFFER_CONFIG"])) {
			$cliArgs = $_SERVER["argv"];
			array_shift($cliArgs);
			$config = new \PHP_CodeSniffer\Config(
				array(
					"--no-colors",
					"--no-cache",
					"--tab-width=4",
					"--standard=" . self::standardDir(),
					"--config-set tab_width 4",
					"--config-set installed_paths " . self::standardDir(),
					"--config-set default_standard MyPSR",
					"--sniffs=" . self::sniffs(),
					$cliArgs[0]
				)
			);

			$GLOBALS["PHP_CODESNIFFER_CONFIG"] = $config;
		}
	}

	/**
	 * Sets up this unit test.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->standardsDir = self::standardDir();
		$this->testsDir     = self::testsDir();
	}//end setUp()

	public static function sniffs()
	{
		$class = str_replace("\\", ".", get_called_class());
		$class = str_replace("Tests.", "", $class);
		return str_replace("UnitTest", "", $class);
	}

	public static function standardDir()
	{
		return str_replace(
			DIRECTORY_SEPARATOR . "Tests",
			"",
			realpath(dirname(__FILE__))
		);
	}

	public static function testsDir()
	{
		return realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
	}

}//end class
