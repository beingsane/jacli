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

	public function __construct(JRegistry $config)
	{
		$this->config = $config;
	}

	abstract public function createAdminUser(AcliModelDatabase $db);

	abstract public function createConfig();

	abstract public function cleanup();

	abstract public function setupDatabase();

	abstract public function getBrowserLinks();

	/**
	 * @param SimpleXMLElement $version
	 * @return AcliApplicationInterface
	 * @throws Exception
	 */
	public function checkSourceDirectory(SimpleXMLElement $version)
	{
		$downloader = new AcliModelDownloader($this->config);
		$sourceDir = $this->config->get('sourceDir');

		switch ($version->type)
		{
			case 'download';
				if (!JFolder::exists($sourceDir))
				{
					$downloader->download($sourceDir, $version);
				}

				break;

			case 'git':
				$downloader->checkoutGit($sourceDir, $version);

				break;

			case 'svn':
				$downloader->checkoutSVN($sourceDir, $version);
				$this->config->set('sourceDir', $sourceDir . '/export');

				break;

			default:
				throw new Exception(sprintf('%s - Unknown repository type: %s'
					, __METHOD__, $version->type), 1);
		}

		return $this;
	}

	/**
	 * @param string $string
	 * @param bool $nl
	 */
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
