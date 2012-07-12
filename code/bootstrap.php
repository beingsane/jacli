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
error_reporting(- 1);
ini_set('display_errors', true);

// Define the path for the Joomla Platform.
if(false == defined('JPATH_PLATFORM'))
{
    $platform = getenv('JOOMLA_PLATFORM_PATH');

    if($platform)
    {
        define('JPATH_PLATFORM', realpath($platform.'/libraries'));
    }
    else
    {
        define('JPATH_PLATFORM', realpath(__DIR__.'/../../joomla/libraries'));
    }
}

// Ensure that required path constants are defined.
defined('JPATH_BASE') || define('JPATH_BASE', realpath(__DIR__));
defined('JPATH_ROOT') || define('JPATH_ROOT', JPATH_BASE);
defined('JACLI_PATH_DATA') || define('JACLI_PATH_DATA', realpath(JPATH_ROOT.'/../data'));

// Import the platform(s).
#require_once JPATH_PLATFORM.'/import.php';
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

// Make sure that the Joomla Platform has been successfully loaded.
if(false == class_exists('JLoader'))
    throw new Exception('Joomla Platform not loaded.', 1);

//@todo deprecate jimports
jimport('joomla.filesystem.folder');

//-- php >= 5.3 !
spl_autoload_register('jacliLoader', true, true);

/**
 * Autoloader.
 *
 * This function was created, because we LOVE Joomla!
 * ..and also want a class prefix beginning with J =;)
 *
 * @param string $className
 *
 * @return mixed
 */
function jacliLoader($className)
{
    if(0 !== strpos($className, 'Jacli'))
        return true;

    $parts = preg_split('/(?<=[a-z])(?=[A-Z])/x', substr($className, 5));

    // If there is only one part we want to duplicate that part for generating the path.
    $parts = (1 === count($parts))
        ? array($parts[0], $parts[0])
        : $parts;

    $path = JPATH_BASE.'/'.strtolower(implode('/', $parts)).'.php';

    if(file_exists($path))
        return include $path;

    return true;
}
