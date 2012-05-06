<?php
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 16:21
 */
class AcliModelDeploy extends JModelBase
{
	private $root = '';

	private $sourceDir = '';

	private $targetDir = '';

	private $target = '';

	/**
	 * @var JApplicationCli
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
			->openInBrowser();

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
		$targetApplication = $this->state->get('application');
		$version = $this->state->get('version');

		$this->root = $this->state->get('httpRoot');
		$this->target = $this->state->get('target');

		$applicationList = AcliApplicationHelper::getApplicationList($this->state);

		$this->sourceDir = PATH_REPOSITORIES . '/' . $targetApplication . '/' . $version;
		$this->targetDir = $this->root . '/' . $this->target;

		$this->out()
			->out(sprintf('Application:      %s', $targetApplication))
			->out(sprintf('Version:          %s', $version))
			->out(sprintf('Source directory: %s', $this->sourceDir))
			->out(sprintf('Target directory: %s', $this->targetDir))
			->out();

		if (!array_key_exists($targetApplication, $applicationList))
			throw new Exception(sprintf('Unknown application: %s', $targetApplication));

		if (!array_key_exists($version, $applicationList[$targetApplication]->versions))
			throw new Exception('Invalid version', 1);

		if (!JFolder::exists($this->root))
			throw new Exception('Invalid httpRoot path', 1);

		if (JFolder::exists($this->targetDir))
			throw new Exception('The target directory must not exist', 1);

		$className = 'AcliApplicationInterface'
			. ucfirst($targetApplication);

		$this->interface = new $className($this->state, $this->sourceDir, $this->targetDir);

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

		if (!JFolder::copy($this->sourceDir, $this->targetDir))
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

	/**
	 * Applying patches.
	 *
	 * @return \AcliModelDeploy
	 */
	private function applyPatches()
	{
		$this->out('Applying patches...');

		$patches = (array) $this->state->get('patches', array());

		foreach ($patches as $patchName)
		{
			$this->out('Applying ' . $patchName . '...');

			$patchFile = $this->state->get('patchDir') . '/' . $patchName;

			if (!file_exists($patchFile))
			{
				$this->out('not found :(');

				continue;
			}

			system("patch -d \"$this->targetDir\" -p0 < \"$patchFile\"");
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

		if ($this->application instanceof JApplicationWeb)
		{
			$this->out('Links:');

			foreach ($links as $linkTitle => $linkUrl)
			{
				$this->out('<a href="' . $httpBase . '/' . $this->target . $linkUrl . '">'
					. $this->target . $linkTitle . '</a>');
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

					system($browserBin . ' ' . $httpBase . '/' . $this->target . $linkUrl . ' &');
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
	private
	function out($text = '', $nl = true)
	{
		$this->application->out($text, $nl);

		return $this;
	}
}
