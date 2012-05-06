<?php
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 14:23
 */
class JConfig
{
	/**
	 * The default application to build.
	 * @var string
	 */
	public $application = 'joomlacms';

	// The default version to build
	public $version = '2.5.4';

	/**
	 * The path where to store the source codes.
	 *
	 * If left blank, a subdirectory of the application will be used.
	 *
	 * @var string
	 */
	public $sourcesPath = '';

	// C:\path\to\your\workspace
	public $httpRoot = '';

	// C:\path\to\browser.exe
	public $browserBin = '';

	/**
	 * Path to git executable
	 * C:\path\to\git.exe
	 * @var string
	 */
	public $gitBin = 'git';

	/**
	 * The interface to use.
	 *
	 * If no interface is specified, the default terminal will be used.
	 *
	 * Possible values: KDE,
	 *
	 * @var string
	 */
	public $interface = '';

	/**
	 * Web path to your workspace
	 * @var string
	 */
	public $httpBase = 'http://localhost';

	public $patchDir = '';

	public $patches = array();

}
