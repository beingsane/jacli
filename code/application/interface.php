<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 00:07
 */
abstract class AcliApplicationInterface
{
	/**
	 * @var JRegistry
	 */
	protected $config;

	/**
	 * @var string
	 */
	protected $name = '';

	protected $sourceDir = '';

	protected $targetDir = '';

	public function __construct(JRegistry $config, $source, $target)
	{
		$this->config = $config;
		$this->sourceDir = $source;
		$this->targetDir = $target;
	}

	abstract public function createAdminUser(AcliModelDatabase $db);

	abstract public function createConfig();

	abstract public function cleanup();

	abstract public function setupDatabase();

	abstract public function getBrowserLinks();

	public function checkSourceDirectory(SimpleXMLElement $version)
	{
		$downloader = new AcliModelDownloader($this->config);

		switch ($version->type)
		{
			case 'download';
				if (!JFolder::exists($this->sourceDir))
				{
					$downloader->download($this->sourceDir, $version);
				}

				break;

			case 'git':
				$downloader->checkoutGit($this->sourceDir, $version);

				break;

			default:
				throw new Exception(sprintf('%s - unknown repository type: %s'
					, __METHOD__, $version->type), 1);
		}

		return $this;
	}

	protected function out($string = '', $nl = true)
	{
		$application = JFactory::getApplication();

		if ($application instanceof JApplicationCli || $application instanceof JApplicationWeb)
		{
			$application->out($string, $nl);
		}
		else
		{
			echo $string;
		}
	}

}
