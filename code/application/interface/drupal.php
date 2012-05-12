<?php
/**
 * User: elkuku
 * Date: 10.05.12
 * Time: 12:40
 */
class JacliApplicationInterfaceDrupal extends JacliApplicationInterface
{
	protected $name = 'joomla-cms';

	public function createAdminUser(JacliModelDatabase $db)
	{
		// TODO: Implement createAdminUser() method.
	}

	public function createConfig()
	{
		// TODO: Implement createConfig() method.
	}

	public function cleanup()
	{
		// TODO: Implement cleanup() method.
	}

	public function setupDatabase()
	{
		// TODO: Implement setupDatabase() method.

		$this->config->set('db_name', $this->config->get('target'));

		$dbModel = new JacliModelDatabase($this->config);

		$this->out(sprintf('Creating database %s ...', $this->config->get('db_name')), false);
		$dbModel->createDB();
		$this->out('ok');

		return $this;
	}

	/**
	 * Displays a result message.
	 *
	 * @return array
	 */
	public function getResultMessage()
	{
		// TODO: Implement getResultMessage() method.

		$message = array();

		$message[] = '';
		$message[] = 'Drupal has been installed succesfully.';
		$message[] = '';
		$message[] = 'Credentials:';
		$message[] = 'Database       : ' . $this->config->get('db_name');
		$message[] = 'Admin user     : ' . $this->config->get('admin_user');
		$message[] = 'Admin password : ' . $this->config->get('admin_password');
		$message[] = '';

		return $message;

	}

	public function getBrowserLinks()
	{
		// TODO: Implement getBrowserLinks() method.

		return array('Drupal site' => '');
	}
}
