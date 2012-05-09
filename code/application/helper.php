<?php
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 05:05
 */

class AcliApplicationHelper
{
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

				$className = 'AcliConfig' . $targetApplication;

				return new $className;
			}
		}

		return $config;
	}

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
				//$this->out('  --' . $k);

				//if (!count($v))
				//	$this->out('    (empty)');

				foreach ($v as $k1 => $v1)
				{
					//	$this->out('    (' . $k1 . ' = ' . $v1 . ')');
				}
			}
			else
			{
				$test = $input->get($k);

				if ($test)
				{
					$config->set($k, $test);
				}
//				$this->out('  --' . $k . ' (' . $v . ')');
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

	public static function parseAppConfig($app)
	{
		$path = JPATH_CONFIGURATION . '/application/' . $app . '.dist.php';

		if( ! file_exists($path))
			throw new Exception(__METHOD__.' - File not found: ' . $path);

		require $path;

		$className = 'AcliConfig' . ucfirst($app);

		if (!class_exists($className))
		throw new Exception(__METHOD__ . 'Config class not found: ' . $className);

		$cfg = new $className;

		$html = array();

		$html[] = '<h3>Application configuration</h3>';

		$html[] = '<ul>';

		$blacks = array('application', 'version', 'interface', 'gitBin', 'browserBin',);

		foreach ($cfg as $k => $v)
		{
			if(in_array($k, $blacks))
				continue;

			$html[] = '<li>';
			$html[] = '<label for="' . $k . '">' . ucfirst($k) . '</label>';
			$html[] = '<input id="' . $k . '" name="' . $k . '" value="' . $v . '"/>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		return implode("\n", $html);


		$xml = JFactory::getXML($path);
	}

}

