<?php
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 04:51
 */

version_compare(PHP_VERSION, '5.3', '>=') || die('This script requires PHP >= 5.3');

// Bootstrap the application.
$path = getenv('JACLI_HOME');

define('JACLI_PATH_TEMPLATE', __DIR__ . '/template');

try
{
	if ($path)
	{
		require_once $path . '/bootstrap.php';
	}
	else
	{
		require_once realpath(__DIR__ . '/../code/bootstrap.php');
	}

	// Set all loggers to echo.
	JLog::addLogger(array('logger' => 'echo'), JLog::ALL);

	// Instantiate the application.
	$application = JApplicationWeb::getInstance('JacliApplicationWeb');

	// Store the application.
	JFactory::$application = $application;

	// Execute the application.
	$application->execute();
}
catch (Exception $e)
{
	// An exception has been caught, just echo the message.
	echo '<p style="color: red">' . $e->getMessage() . '</p>';

	exit($e->getCode());
}
