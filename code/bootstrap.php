<?php
/**
 * Bootstrap file for the Application.
 *
 * @package     JACLI.Application
 *
 * @copyright   Copyright (C) {COPYRIGHT}. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// Set the Joomla execution flag.
define('_JEXEC', 1);

// Allow the application to run as long as is necessary.
ini_set('max_execution_time', 0);

// Note, you would not use these settings in production.
error_reporting(-1);
ini_set('display_errors', true);

// Define the path for the Joomla Platform.
if (!defined('JPATH_PLATFORM'))
{
	$platform = getenv('JOOMLA_PLATFORM_PATH');

	if ($platform)
	{
		define('JPATH_PLATFORM', realpath($platform . '/libraries'));
	}
	else
	{
		define('JPATH_PLATFORM', realpath(__DIR__ . '/../../joomla/libraries'));
	}
}

// Ensure that required path constants are defined.
defined('JPATH_BASE')      || define('JPATH_BASE', realpath(__DIR__));
defined('JPATH_ROOT')      || define('JPATH_ROOT', JPATH_BASE);
defined('JACLI_PATH_DATA') || define('JACLI_PATH_DATA', realpath(JPATH_ROOT . '/../data'));

// Import the platform(s).
require_once JPATH_PLATFORM . '/import.php';

// Make sure that the Joomla Platform has been successfully loaded.
if (!class_exists('JLoader'))
	throw new Exception('Joomla Platform not loaded.', 1);

// Setup the autoloader for the JaCLI application classes.
JLoader::registerPrefix( /*J*/'Acli', __DIR__);

define('COLORS', class_exists('Console_Color'));

//@todo deprecate jimports
jimport('joomla.filesystem.folder');
