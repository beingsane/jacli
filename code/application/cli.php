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
AcliApplicationCli extends JApplicationCli
{
	private $verbose = true;

	/**
	 * @var AcliUserinterface
	 */
	private $userInterface = null;

	public function  doExecute()
	{
		$this->verbose = ($this->input->get('q', $this->input->get('quiet'))) ? false : true;

		$this->out('JACLI - A Joomla! CLI.');

		AcliApplicationHelper::getOverrides($this->config);

		if ($this->input->get('h', $this->input->get('help')))
		{
			$this->help();

			return;
		}

		$className = 'AcliUserinterface' . ucfirst($this->config->get('interface', 'cli'));

		$this->userInterface = new $className;

		try
		{
			$model = new AcliModelDeploy($this->config);

			if($this->input->get('listtargets'))
			{
				$model->listTargets();
			}
			elseif($this->input->get('deletetarget'))
			{
				$model->deleteTarget();
			}
			elseif($this->input->get('listapps'))
			{
				$model->listApplications();
			}
			else
			{
				$model->deploy();

			}

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
	 * @throws RuntimeException
	 * @return  object  An object to be loaded into the application configuration.
	 *
	 * @since   1.0
	 */
	protected function fetchConfigurationData($file = '', $class = 'JConfig')
	{
		return AcliApplicationHelper::fetchConfigurationData($this->input->get('application'));
	}

	/**
	 * Display a help message
	 *
	 * @return \AcliApplicationCli
	 */
	private function help()
	{
		$cfg = $this->config->toObject();

		$this->out('Usage:    jacli.php [switches]');
		$this->out();
		$this->out('  -h  --help   Prints this usage information.');
		$this->out('  -q  --quiet  Do not produce any output (except errors).');
		$this->out();
		$this->out('  --install  Install an application - This is the default action.');
		$this->out('  --listtargets  Lists all targets under the given httpRoot.');
		$this->out('  --deletetarget <target>  Deletes a target.');
		$this->out('  --listapps  Lists all known applications.');
		$this->out();
		$this->out('Required:');
		$this->out('  --target  Specify a target directory.');
		$this->out();
		$this->out('Overrideable:');

		foreach ($cfg as $k => $v)
		{
			if (in_array($k, array('execution', 'cwd')))
				continue;

			if (is_object($v) || is_array($v))
			{
				$this->out('  --' . $k);

				if (!count($v))
					$this->out('    (empty)');

				foreach ($v as $k1 => $v1)
				{
					$this->out('    (' . $k1 . ' = ' . $v1 . ')');
				}
			}
			else
			{
				$this->out('  --' . $k . ' (' . $v . ')');
			}
		}

		if ('' == $this->config->get('application'))
		{
			$this->out();
			$this->out('!! NOTE !! For application specific overrides use: jacli --help --application <application>');
		}

		$this->out();
		$this->out('Optional:');
		$this->out('  --updaterepo  Update the repository (if applicable)');

		$this->out();
		$this->out(str_repeat('_', 80));
		$this->out('Examples:');
		$this->out();
		$this->out('jacli --target test1');
		$this->out('  Deploys an application to the target \'test1\' according to the options specified in the configuration.');
		$this->out();
		$this->out('jacli --target test1 --version git --updaterepo');
		$this->out('  Deploys the version \'git\' of an application, updating the sources first.');

		return $this;
	}

	/**
	 * Write a string to standard output.
	 *
	 * @param string $text
	 * @param bool $nl
	 * @return AcliApplicationCli|JApplicationCli
	 */
	public function out($text = '', $nl = true)
	{
		return ($this->verbose) ? parent::out($text, $nl) : $this;
	}

	/**
	 * Get a value from the user using the given user interface.
	 * @param $message
	 * @param string $type
	 * @param array $values
	 * @param bool $required
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
	 * @param mixed $message array or string
	 * @param string $type
	 * @return AcliApplicationCli
	 */
	public function displayMessage($message, $type = 'message')
	{
		$this->userInterface->displayMessage($message, $type);

		return $this;
	}
}
