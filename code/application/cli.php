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
class /*J*/AcliApplicationCli extends JApplicationCli
{
	private $verbose = true;

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

		$target = $this->input->get('target');

		if( ! $target)
		{
			switch($this->config->get('interface'))
			{
				case 'KDE':
				case 'kde':
					$target = shell_exec('kdialog --title "Target directory" --inputbox "The name of the target directory:"');

					die($target);
					break;
			}

			// Use echo
			echo 'Target directory: ';
			$target = $this->in();

			if( ! $target)
				exit;
		}

		if (!$target)
			throw new Exception('Please specify a target directory with --target', 1);

		$this->config->set('target', $target);

		$model = new AcliModelDeploy($this->config);

		$model->deploy();
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

	private function help()
	{
		$cfg = $this->config->toObject();

		$this->out('Usage:    jacli.php [switches]');
		$this->out();
		$this->out('  -h  --help   Prints this usage information.');
		$this->out('  -q  --quiet  Do not produce any output (except errors).');
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

		$this->out();
		$this->out('Optional:');
		$this->out('  --updaterepo  Update the repository (if applicable)');

		$this->out();
		$this->out('Examples:');
		$this->out();
		$this->out('jacli --target test1');
		$this->out('  Deploys an application to the target \'test1\' according to the options specified in the configuration.');
		$this->out();
		$this->out('jacli --target test1 --version git --updaterepo');
		$this->out('  Deploys the version \'git\' of an application, updating the sources first.');

	}

	public function out($text = '', $nl = true)
	{
		return ($this->verbose) ? parent::out($text, $nl) : $this;
	}
}
