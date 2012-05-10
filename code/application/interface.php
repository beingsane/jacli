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

	public function __construct(JRegistry $config)
	{
		$this->config = $config;
	}

	abstract public function createAdminUser(AcliModelDatabase $db);

	abstract public function createConfig();

	abstract public function cleanup();

	abstract public function setupDatabase();

	/**
	 * Displays a result message.
	 *
	 * @return AcliApplicationInterface
	 */
	abstract public function getResultMessage();

	abstract public function getBrowserLinks();

	/**
	 * @param string           $targetApplication
	 * @param SimpleXMLElement $version
	 *
	 * @throws Exception
	 * @return AcliApplicationInterface
	 */
	public function checkSourceDirectory($targetApplication, SimpleXMLElement $version)
	{
		$sourceDir = PATH_REPOSITORIES . '/' . $targetApplication . '/' . $version->version;

		$downloader = new AcliModelDownloader($this->config);
//		$sourceDir = $this->config->get('sourceDir');

		switch ($version->type)
		{
			case 'download';
				if (!JFolder::exists($sourceDir))
					$downloader->download($sourceDir, $version);

			//	$subDir = (string) $version->subfolder;

				if ($version->subfolder)
					$sourceDir .= '/' . $version->subfolder;

				break;

			case 'git':
				$downloader->checkoutGit($sourceDir, $version);

				break;

			case 'svn':
				$downloader->checkoutSVN($sourceDir, $version);

				$sourceDir .= '/export';

				break;

			default:
				throw new Exception(sprintf('%s - Unknown repository type: %s'
					, __METHOD__, $version->type), 1);
		}

		return $sourceDir;
	}

	/**
	 * @param string $string
	 * @param bool   $nl
	 *
	 * @return \AcliApplicationInterface
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

		return $this;
	}

}
