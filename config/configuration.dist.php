<?php
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 14:23
 */
class JConfig
{
	// Database credentials
	public $db_type = 'mysqli';
	public $db_host = 'localhost';
	public $db_user = 'root';
	public $db_pass = '';
	public $db_prefix = '';

	/**
	 * The default application to build (e.g. joomlacms).
	 *
	 * If left blank, a selector will be displayed.
	 *
	 * @var string
	 */
	public $application = '';

	/**
	 * The default version to build (e.g. development).
	 *
	 * If left blank, a selector will be displayed.
	 *
	 * @var string
	 */
	public $version = '';

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

	/**
	 * Web path to your workspace
	 *
	 * http://localhost
	 *
	 * @var string
	 */
	public $httpBase = '';

	// C:\path\to\browser.exe
	public $browserBin = '';

	/**
	 * Path to git executable
	 *
	 * C:\path\to\git.exe
	 *
	 * @var string
	 */
	public $gitBin = '';

	/**
	 * The interface to use.
	 *
	 * If no interface is specified, the default terminal will be used.
	 *
	 * Possible values: CLI (default), KDE, Gnome, ...
	 *
	 * @var string
	 */
	public $interface = 'cli';

    /**
     * Should be left blank.
     *
     * @var string
     */
    public $target = '';
}
