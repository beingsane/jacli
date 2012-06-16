<?php defined('_JEXEC') || die('=;)');
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 13:18
 */

/**
 * JACLI
 *
 * - JApplicationCLI
 * - Jack Lee.
 * - *J*ack of *A*ll trades (even *CLI*)
 *
 *
 * http://www.youtube.com/watch?v=Z4CRwrR_lBE
 */
class /*J*/
JacliApplicationCli extends JApplicationCli
{
	private $verbose = true;

	/**
	 * @var JacliUserinterface
	 */
	private $userInterface = null;

	public function  doExecute()
	{
		if($this->input->get('nocolors'))
		{
			define('COLORS', 0);
		}
		else
		{
			//-- Got xampp and probs setting the include path ? eclipse ?..
			set_include_path(get_include_path().PATH_SEPARATOR.'/opt/lampp/lib/php');

			//-- El KuKu's ConsoleColor - see: http://elkuku.github.com/pear/
			//@include 'elkuku/console/Color.php';

			//-- OR -- PEAR's ConsoleColor
			if( ! class_exists('Console_Color2')) @include 'Console/Color2.php';

			//-- Any color ?
			define('COLORS', class_exists('Console_Color2'));
		}

		$this->verbose = ($this->input->get('q', $this->input->get('quiet'))) ? false : true;

		$this->output('JACLI - A Joomla! CLI.', true, '', '', 'bold');

		JacliApplicationHelper::getOverrides($this->config);

		if ($this->input->get('h', $this->input->get('help'))
			|| empty($this->input->args[0])
		)
		{
			$this->help();

			return;
		}

		$className = 'JacliUserinterface' . ucfirst($this->config->get('interface', 'cli'));

		$this->userInterface = new $className;

		try
		{
			// Get the controller instance based on the request.
			$controller = $this->fetchController();

			// Execute the controller.
			$controller->execute();
		}
		catch (Exception $e)
		{
			$this->userInterface->displayMessage($e->getMessage(), 'error');
		}
	}

    /**
     * Fetch the configuration data for the application.
     *
     * @param string $file
     * @param string $class
     *
     * @return mixed|object An object to be loaded into the application configuration.
     * @since   1.0
     */
	protected function fetchConfigurationData($file = '', $class = 'JConfig')
	{
		return JacliApplicationHelper::fetchConfigurationData($this->input->get('application'));
	}

	/**
	 * Display a help message
	 *
	 * @return \JacliApplicationCli
	 */
	private function help()
	{
		$cfg = $this->config->toObject();

		$this->output()
			->output('========================================')
			->output('Usage:    ')
			->output('jacli', false, '', '', 'bold')
			->output(' <command>', false, 'green')
			->output(' [switches]', true, 'yellow')
			->output('========================================')
			->output()
			->output('Commands', true, 'green')
			->output('========')
			->output('  install       Install an application.')
			->output('  listapps      Lists all known applications.')
			->output('  listtargets   Lists all targets under the given httpRoot.')
			->output('  deletetarget  <target>  Deletes a target.')
			->output()
			->output('Switches', true, 'yellow')
			->output('========')
			->output('  -h  --help   Prints this usage information.')
			->output('  -q  --quiet  Do not produce any output (except errors).')
			->output()
			->output('  --target  Specify a target directory.')
			->output()
			->output('Overrideable configuration values', true, 'cyan')
			->output('============');

		foreach ($cfg as $k => $v)
		{
			if (in_array($k, array('execution', 'cwd')))
				continue;

			if (is_object($v) || is_array($v))
			{
				$this->output('  --' . $k);

				if (!count($v))
					$this->output('    (empty)');

				foreach ($v as $k1 => $v1)
				{
					$this->output('    (' . $k1 . ' = ' . $v1 . ')');
				}
			}
			else
			{
				$this->output('  --' . $k . ' (' . $v . ')');
			}
		}

		if ('' == $this->config->get('application'))
		{
			$this->output()
				->output('NOTE:', false, '', '', 'bold')
				->output(' For application specific overrides use: jacli --help --application <application>');
		}

		$this->output()
			->output('Optional', true, 'brown')
			->output('========')
			->output('  --updaterepo  Update the repository (if applicable)')
			->output()
			->output(str_repeat('_', 80))
			->output('Examples', true, '', '', 'bold')
			->output('========')
			->output()
			->output('jacli install --target test1', true, 'green')
			->output('  Deploys an application to the target "test1" with to the options specified in the configuration.')
			->output('  Settings that have not been specified in the configuration will be "asked" according to the specified', false)
			->output(' interface', true, '', '', 'bold')
			->output()
			->output('jacli install --target test1 --version development --updaterepo', true, 'green')
			->output('  Deploys the version "development" of an application to the target "test1", updating the sources first.')
			->output()
			->output('jacli install --application joomlacms --version 4.0 --target test1', true, 'green')
			->output('  Deploys the version "4.0" of the application Joomla! CMS" to the target "test1" (if available ;) ).')
			->output()
			->output('have Fun =;)', true, 'green', '', 'bold');

		return $this;
	}

	/**
	 * Write a string to standard output.
	 *
	 * @param string $text The text to display
	 * @param bool   $nl   Should a new line be printed.
	 * @param string $fg   Foreground color.
	 * @param string $bg   Background color.
	 * @param string $style
	 *
	 * @return \JacliApplicationCli|\JApplicationCli
	 */
	public function output($text = '', $nl = true, $fg = '', $bg = '', $style = '')
	{
		if(false == $this->verbose)
			return $this;

		static $color = null;

		if(is_null($color))
			$color = new Console_Color2;

		if($fg && COLORS) $this->out($color->fgcolor($fg), false);
		if($bg && COLORS) $this->out($color->bgcolor($bg), false);

		if($style && COLORS)
		{
			$cs = $color->getColorCodes();
		//	var_dump($cs);
			$this->out("\033[".$cs['style'][$style].'m', false);
		}

		$this->out($text, $nl);

		if(($fg || $bg || $style) && COLORS) $this->out($color->convert('%n'), false);

		return $this;
	}

	/**
	 * Get a value from the user using the given user interface.
	 *
	 * @param        $message
	 * @param string $type
	 * @param array  $values
	 * @param bool   $required
	 *
	 * @return mixed|string
	 * @throws Exception
	 */
	public function getUserInput($message, $type = 'text', array $values = array(), $required = true)
	{
		$retVal = $this->userInterface->getUserInput($message, $type, $values);

		$retVal = trim($retVal);

		if (!$retVal && $required)
			throw new Exception('User abort', 666);

		return $retVal;
	}

	/**
	 * Displays a message using the given user interface.
	 *
	 * @param mixed  $message array or string
	 * @param string $type
	 *
	 * @return JacliApplicationCli
	 */
	public function displayMessage($message, $type = 'message')
	{
		$this->userInterface->displayMessage($message, $type);

		return $this;
	}

	/**
	 * Method to get a controller object based on the command line input.
	 *
	 * @return  JControllerBase
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function fetchController()
	{
		$base = 'JacliController';

		$sub = $this->input->args[0];

		$className = $base . ucfirst($sub);

		// If the requested controller exists let's use it.
		if (class_exists($className))
		{
			return new $className($this->input, $this);
		}

		// Nothing found. Panic.
		throw new InvalidArgumentException('Unable to handle the request for route: ' . $this->input->args[0], 400);
	}

	public function getConfig()
	{
		return $this->config->toObject();
	}

}
