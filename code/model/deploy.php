<?php
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 16:21
 */
class JacliModelDeploy extends JModelBase
{
	/**
	 * @var JacliApplicationCli
	 */
	private $application;

	/**
	 * @var JacliApplicationInterface
	 */
	private $interface = null;

	/**
	 * Constructor.
	 *
	 * @param JRegistry|null $state
	 */
	public function __construct(JRegistry $state = null)
	{
		parent::__construct($state);

		$this->application = JFactory::getApplication();

		$this->setupLog();
	}

	/**
	 * @return JacliModelDeploy
	 */
	public function deploy()
	{
		$this->setup()
			->copyFiles()
			->setupDatabase()
			->createConfig()
			->cleanup()
			->applyPatches()
			->openInBrowser()
			->displayResult();

		$this->out()
			->out(sprintf('Finished'));

		return $this;
	}

	public function listTargets()
	{
		$root = $this->checkRootPath();

		$targets = JFolder::folders($root);

		$this->application->out('Targets on: ' . $root);

		$this->application->out(print_r($targets, 1));
	}


	public function deleteTarget()
	{
		$root = $this->checkRootPath();
		$target = $this->application->input->get('target');

		if (!$target)
			$target = $this->getUserInput('target', 'The name of the target directory:');

		$targetDir = $root . '/' . $target;

		if (!JFolder::exists($targetDir))
			throw new Exception('The target directory does not exist', 1);

		$model = new JacliModelDatabase($this->state);

		$this->application->out(sprintf('Deleting target: %s', $target));

		try
		{
			$this->application->out('Deleting the database');
			$model->deleteDb($target);
		}
		catch (UnexpectedValueException $e)
		{
			$this->application->out('A database has not been found !');
			//throw new Exception($e);
		}

		$this->application->out(sprintf('Deleting the folder: %s', $targetDir));

		if (!JFolder::delete($targetDir))
			throw new Exception(sprintf(
					'Unable to delete the folder: %s', $targetDir)
				, 1);

		$this->application->out('The target has been deleted');
	}

	/**
	 * @return mixed|string
	 * @throws Exception
	 */
	private function checkRootPath()
	{
		$root = $this->getUserInput('httpRoot', 'The http root directory');

		if (!JFolder::exists($root))
			throw new Exception('Invalid httpRoot path', 1);

		return $root;
	}

	/**
	 * Get input from the user.
	 *
	 * @param        $vName
	 * @param        $message
	 * @param string $type
	 * @param array  $values
	 * @param bool   $required
	 *
	 * @return mixed|string
	 */
	private function getUserInput($vName, $message, $type = 'text', array $values = array(), $required = true)
	{
		$v = $this->state->get($vName);

		if (!$v)
		{
			$v = $this->application->getUserInput($message, $type, $values, $required);

			$this->state->set($vName, $v);
		}

		return $v;
	}

	/**
	 * @return JacliModelDeploy
	 * @throws Exception
	 */
	private function setup()
	{
		$applicationList = JacliApplicationHelper::getApplicationList();

		//-- Application
		$targetApplication = $this->state->get('application');

		if (!$targetApplication)
		{
			$targetApplication = $this->application->getUserInput('Application to build'
				, 'mchoice', array_keys($applicationList));

			//-- Reload config
			$cfg = new JRegistry(JacliApplicationHelper::fetchConfigurationData($targetApplication));

			JacliApplicationHelper::getOverrides($cfg);
			$this->state = $cfg;
		}

		if (!array_key_exists($targetApplication, $applicationList))
			throw new Exception(sprintf('Unknown application: %s', $targetApplication));

		$version = $this->getUserInput('version', sprintf('Select a %s version', $targetApplication)
			, 'mchoice', array_keys($applicationList[$targetApplication]->versions));

		if (!array_key_exists($version, $applicationList[$targetApplication]->versions))
			throw new Exception('Invalid version', 1);

		if ('development' == $version)
		{
			if ('0' === $this->application->getUserInput('Update the repository ?', 'yesno', array(), false))
				$this->application->input->set('updaterepo', 1);
		}

		$root = $this->checkRootPath();

		//-- Target
		$target = $this->getUserInput('target', 'The name of the target directory');

		$targetDir = $root . '/' . $target;

		if (JFolder::exists($targetDir))
			throw new Exception('The target directory must not exist', 1);

		$this->state->set('targetDir', $targetDir);

		$className = 'JacliApplicationInterface' . ucfirst($targetApplication);

		$this->interface = new $className($this->state);

		$sourceDir = $this->interface->checkSourceDirectory($targetApplication, $applicationList[$targetApplication]->versions[$version]);
		$this->state->set('sourceDir', $sourceDir);

		$message = array();

		$message[] = 'Ready to install:';
		$message[] = sprintf('Application:      %s', $targetApplication);
		$message[] = sprintf('Version:          %s', $version);
		$message[] = sprintf('Source directory: %s', $sourceDir);
		$message[] = sprintf('Target directory: %s', $targetDir);

		$this->application->displayMessage($message);

		JLog::add(implode("\n", $message));

		return $this;
	}

	/**
	 * @return JacliModelDeploy
	 * @throws Exception
	 */
	private function copyFiles()
	{
		$this->out('Copying files...', false);

		if (!JFolder::copy($this->state->get('sourceDir'), $this->state->get('targetDir')))
		{
				throw new Exception(sprintf(
						'Unable to copy the folder %s to %s'
						, $this->state->get('sourceDir'), $this->state->get('targetDir'))
					, 1);
		}

		$this->out('ok');

		return $this;
	}

	/**
	 * @return JacliModelDeploy
	 * @throws Exception
	 */
	private function setupDatabase()
	{
		$this->interface->setupDatabase();

		return $this;
	}

	/**
	 * Create configuration.php
	 * @return \JacliModelDeploy
	 */
	private function createConfig()
	{
		$this->out('Creating configuration...', false);

		$this->interface->createConfig();

		$this->out('ok');

		return $this;
	}

	/**
	 * @return JacliModelDeploy
	 */
	private function cleanup()
	{
		$this->interface->cleanup();

		return $this;
	}

	/**
	 * Display the result.
	 *
	 * @return JacliModelDeploy
	 */
	private function displayResult()
	{
		$this->application->displayMessage($this->interface->getResultMessage());

		return $this;
	}

	/**
	 * Applying patches.
	 *
	 * @return \JacliModelDeploy
	 */
	private function applyPatches()
	{
		$this->out('Applying patches...');

		$patches = (array) $this->state->get('patches', array());
		$targetDir = $this->state->get('targetDir');

		foreach ($patches as $patchName)
		{
			$this->out('Applying ' . $patchName . '...');

			$patchFile = $this->state->get('patchDir') . '/' . $patchName;

			if (!file_exists($patchFile))
			{
				$this->out('not found :(');

				continue;
			}

			system("patch -d \"$targetDir\" -p0 < \"$patchFile\"");
		}

		$this->out('ok');

		return $this;
	}

	/**
	 * Open in browser.
	 *
	 * @return \JacliModelDeploy
	 */
	private function openInBrowser()
	{
		$browserBin = $this->state->get('browserBin');
		$httpBase = $this->state->get('httpBase');
		$links = $this->interface->getBrowserLinks();
		$target = $this->state->get('target');

		if ($this->application instanceof JApplicationWeb)
		{
			$this->out('Links:');

			foreach ($links as $linkTitle => $linkUrl)
			{
				$this->out('<a href="' . $httpBase . '/' . $target . $linkUrl . '">'
					. $target . $linkTitle . '</a>');
			}
		}
		else
		{
			if ($browserBin)
			{
				$this->out('Open in browser: ' . $browserBin);

				foreach ($links as $linkTitle => $linkUrl)
				{
					$this->out('Open ' . $linkTitle);

					system($browserBin . ' ' . $httpBase . '/' . $target . $linkUrl . ' &');
				}
			}
			else
			{
				$this->out('No browser specified');
			}
		}

		return $this;
	}

	/**
	 * @param string $text
	 * @param bool   $nl
	 *
	 * @return JacliModelDeploy
	 */
	private function out($text = '', $nl = true)
	{
		$this->application->out($text, $nl);

		JLog::add($text);

		return $this;
	}

	/**
	 * Set up the log file.
	 *
	 * @return \JacliModelDeploy
	 */
	private function setupLog()
	{
		jimport('joomla.filesystem.file');

		$fileName = 'log.php';
		$entry = '';

		if ('preserve' == JFactory::getApplication()->input->get('logMode')
			&& JFile::exists(JACLI_PATH_DATA . '/' . $fileName)
		)
		{
			$entry = '----------------------------------------------';
		}
		elseif (JFile::exists(JACLI_PATH_DATA . '/' . $fileName))
		{
			JFile::delete(JACLI_PATH_DATA . '/' . $fileName);
		}

		JLog::addLogger(
			array(
				'text_file_path' => JACLI_PATH_DATA
			, 'text_file' => $fileName
			, 'text_entry_format' => '{DATETIME}	{PRIORITY}	{MESSAGE}'
			, 'text_file_no_php' => true
			)
			, JLog::INFO | JLog::ERROR
		);

		if ('' != $entry)
			JLog::add($entry);

		return $this;
	}


}
