<?php
/**
 * User: elkuku
 * Date: 06.05.12
 * Time: 13:44
 */

abstract class JacliUserinterface
{
	/**
	 * @var JApplicationCli
	 */
	protected $application;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->application = JFactory::getApplication();
	}

	/**
	 * Get a value from the user.
	 *
	 * @param string $message
	 * @param string $type
	 * @param array  $values
	 *
	 * @return mixed
	 */
	abstract public function getUserInput($message, $type = 'text', array $values = array());

	/**
	 * Display an error message.
	 *
	 * @param mixed $message array or string
	 * @param string $type
	 *
	 * @throws UnexpectedValueException
	 * @return JacliUserinterface
	 */
	abstract public function displayMessage($message, $type = 'message');
}
