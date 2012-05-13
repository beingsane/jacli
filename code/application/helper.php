<?php
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 05:05
 */

class JacliApplicationHelper
{
	/**
	 * Fetch the configuration data.
	 *
	 * @param $targetApplication
	 *
	 * @return JConfig
	 *
	 * @throws RuntimeException
	 */
	public static function fetchConfigurationData($targetApplication)
	{
		// Ensure that required path constants are defined.
		if (!defined('JPATH_CONFIGURATION'))
		{
			define('JPATH_CONFIGURATION', realpath(dirname(JPATH_BASE) . '/config'));
		}

		// Set the configuration file path for the application.
		if (file_exists(JPATH_CONFIGURATION . '/configuration.php'))
		{
			$file = JPATH_CONFIGURATION . '/configuration.php';
		}
		else
		{
			// Default to the distribution configuration.
			$file = JPATH_CONFIGURATION . '/configuration.dist.php';
		}

		if (!is_readable($file))
		{
			throw new RuntimeException('Configuration file does not exist or is unreadable.', 1);
		}

		include_once $file;

		$config = new JConfig;

		$targetApplication = $targetApplication ? : $config->application;

		if ($targetApplication)
		{
			$path = JPATH_CONFIGURATION . '/application/' . $targetApplication;

			$file = '';

			if (file_exists($path . '.php'))
			{
				$file = $path . '.php';
			}
			elseif (file_exists($path . '.dist.php'))
			{
				$file = $path . '.dist.php';
			}

			if ($file)
			{
				require $file;

				$className = 'JacliConfig' . ucfirst($targetApplication);

				return new $className;
			}
		}

		return $config;
	}

	/**
	 * Get overrides from user input.
	 *
	 * @param JRegistry $config
	 */
	public static function getOverrides(JRegistry $config)
	{
		$cfg = $config->toObject();

		$input = JFactory::getApplication()->input;

		foreach ($cfg as $k => $v)
		{
			if (in_array($k, array('execution', 'cwd', 'uri')))
				continue;

			if (is_object($v) || is_array($v))
			{
				//@todo override for objects...
			}
			else
			{
				$test = $input->get($k, false, 'var');

				if ($test)
				{
					$config->set($k, $test);
				}
			}
		}

		//	$path = $config->get('sourcesPath');

		defined('PATH_REPOSITORIES') || define('PATH_REPOSITORIES'
		, $config->get('sourcesPath')
			? : dirname(JPATH_BASE) . '/repositories');
	}

	/**
	 * Get a list of known applications.
	 *
	 * @static
	 * @return array
	 * @throws Exception
	 */
	public static function getApplicationList()
	{
		static $applicationList = array();

		if (count($applicationList))
			return $applicationList;

		$files = JFolder::files(JPATH_CONFIGURATION . '/repositories');

		if (false == $files || !count($files))
			throw new Exception(__METHOD__ . ' - No repositories defined');

		foreach ($files as $file)
		{
			$xml = JFactory::getXML(JPATH_CONFIGURATION . '/repositories/' . $file);

			if (!$xml)
				throw new Exception('Invalid repositories file');

			/* @var SimpleXMLElement $repository */
			$r = new stdClass;
			$r->name = (string) $xml->name;

			$vs = array();

			foreach ($xml->versions->version as $version)
			{
				$vs[(string) $version->version] = $version;
			}

			$r->versions = $vs;

			$applicationList[preg_replace('#\.[^.]*$#', '', $file)] = $r;
		}

		return $applicationList;
	}

	/**
	 * Parse the application configuration.
	 *
	 * @param $app
	 *
	 * @return string
	 *
	 * @throws Exception
	 */
	public static function parseAppConfig($app)
	{
		$path = JPATH_CONFIGURATION . '/application/' . $app;

		if (file_exists($path . '.php'))
		{
			require $path . '.php';
		}
		elseif (file_exists($path . '.dist.php'))
		{
			require $path . '.dist.php';
		}
		else
		{
			throw new Exception(__METHOD__ . ' - No configuration file found in: ' . $path);
		}

		$className = 'JacliConfig' . ucfirst($app);

		if (!class_exists($className))
			throw new Exception(__METHOD__ . 'Config class not found: ' . $className);

		return new $className;
	}

}

