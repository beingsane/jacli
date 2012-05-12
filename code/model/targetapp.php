<?php
/**
 * User: elkuku
 * Date: 11.05.12
 * Time: 12:21
 */
class JacliModelTargetapp extends JModelBase
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
		$this->application = JFactory::getApplication();

		parent::__construct($state);
	}

	/**
	 * Get a list of installed applications.
	 *
	 * @return JacliModelDeploy
	 */
	public function listApplications()
	{
		$list = JacliApplicationHelper::getApplicationList();

		$message = array();

		$message[] = 'Installed Applications';
		$message[] = '';

		foreach ($list as $app)
		{
			$message[] = $app->name;

			foreach ($app->versions as $version)
			{
				$message[] = '  ' . $version->version . ' (' . $version->type . ')';
			}
		}

		$message[] = '';

		$this->application->displayMessage($message);

		return $this;
	}

}
