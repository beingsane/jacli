<?php
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 16:21
 */
class AcliModelDeploy extends JModelBase
{
	/**
	 * @var AcliApplicationCli
	 */
	private $application;

	/**
	 * @var AcliApplicationInterface
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
	}

	/**
	 * @return AcliModelDeploy
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

	/**
	 * @return AcliModelDeploy
	 * @throws Exception
	 */
	private function setup()
	{
		$applicationList = AcliApplicationHelper::getApplicationList();

		//-- Application
		$targetApplication = $this->state->get('application');

		if (!$targetApplication)
		{
			$targetApplication = $this->application->getUserInput('Application to build'
				, 'mchoice', array_keys($applicationList));

			//-- Reload config
			$cfg = new JRegistry(AcliApplicationHelper::fetchConfigurationData($targetApplication));

			AcliApplicationHelper::getOverrides($cfg);
			$this->state = $cfg;
		}

		if (!array_key_exists($targetApplication, $applicationList))
			throw new Exception(sprintf('Unknown application: %s', $targetApplication));

		//-- Version
		$version = $this->state->get('version');

		if (!$version)
		{
			$version = $this->application->getUserInput('Select a version'
				, 'mchoice', array_keys($applicationList[$targetApplication]->versions));

			$this->state->set('version', $version);
		}

		if (!array_key_exists($version, $applicationList[$targetApplication]->versions))
			throw new Exception('Invalid version', 1);

//		$updaterepo = $this->application->input->get('updaterepo');

		if ('development' == $version)
		{
			if ('0' === $this->application->getUserInput('Update the repository ?', 'yesno', array(), false))
			{
				$this->application->input->set('updaterepo', 1);
//				$this->state->set('updaterepo', 1);
			}
		}

		//-- http root
		$root = $this->state->get('httpRoot');

		if (!$root)
		{
			$root = $this->application->getUserInput('The http root directory:');

			$this->state->set('httpRoot', $root);
		}

		if (!JFolder::exists($root))
			throw new Exception('Invalid httpRoot path', 1);

		//-- Target
		$target = $this->application->input->get('target');

		if (!$target)
			$target = $this->application->getUserInput('The name of the target directory:');

		$this->state->set('target', $target);


		$targetDir = $root . '/' . $target;

		if (JFolder::exists($targetDir))
			throw new Exception('The target directory must not exist', 1);

		$sourceDir = PATH_REPOSITORIES . '/' . $targetApplication . '/' . $version;

		$this->state->set('sourceDir', $sourceDir);
		$this->state->set('targetDir', $targetDir);

		$message = array();

		$message[] = sprintf('Application:      %s', $targetApplication);
		$message[] = sprintf('Version:          %s', $version);
		$message[] = sprintf('Source directory: %s', $sourceDir);
		$message[] = sprintf('Target directory: %s', $targetDir);

		$this->application->displayMessage(implode("\n", $message));

		$className = 'AcliApplicationInterface' . ucfirst($targetApplication);

		$this->interface = new $className($this->state);

		$this->interface->checkSourceDirectory($applicationList[$targetApplication]->versions[$version]);

		return $this;
	}

	/**
	 * @return AcliModelDeploy
	 * @throws Exception
	 */
	private function copyFiles()
	{
		$this->out('Copying files...', false);

		if (!JFolder::copy($this->state->get('sourceDir'), $this->state->get('targetDir')))
		{
			//@todo legacy
			throw new Exception(JError::getError(), 1);
		}

		$this->out('ok');

		return $this;
	}

	/**
	 * @return AcliModelDeploy
	 * @throws Exception
	 */
	private function setupDatabase()
	{
		$this->interface->setupDatabase();

		return $this;
	}

	/**
	 * Create configuration.php
	 * @return \AcliModelDeploy
	 */
	private function createConfig()
	{
		$this->out('Creating configuration...', false);

		$this->interface->createConfig();

		$this->out('ok');

		return $this;
	}

	/**
	 * @return AcliModelDeploy
	 */
	private function cleanup()
	{
		$this->interface->cleanup();

		return $this;
	}

	private function displayResult()
	{
		$this->application->displayMessage($this->interface->getResultMessage());

		return $this;
	}

	/**
	 * Applying patches.
	 *
	 * @return \AcliModelDeploy
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
	 * @return \AcliModelDeploy
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
	 * @return AcliModelDeploy
	 */
	private function out($text = '', $nl = true)
	{
		$this->application->out($text, $nl);

		return $this;
	}
}
