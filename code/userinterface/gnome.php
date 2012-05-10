<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 17:47
 */

/**
 * A Gnome interface.
 */
class AcliUserinterfaceGnome extends AcliUserinterface
{
	/**
	 * Get a value from the user.
	 *
	 * @param string $message
	 * @param string $type
	 * @param array  $values
	 *
	 * @throws UnexpectedValueException
	 *
	 * @return mixed
	 */
	public function getUserInput($message, $type = 'text', array $values = array())
	{
		switch ($type)
		{
			case 'text':
				return shell_exec('zenity --entry --text="' . $message . '"');

				break;

			case 'mchoice':
				$vs = "\n".implode(" \\\n", $values);

				return shell_exec("zenity --list --title=\"$message\" --column=\"$message\" \\$vs");

				break;

			case 'yesno':
				system('zenity --question --text="' . $message . '"', $ret);

				return $ret;
				break;

			default:
				throw new UnexpectedValueException(__METHOD__ . ' - Invalid input type');
		}
	}

	/**
	 * Display an error message.
	 *
	 * @param mixed $message array or string
	 * @param string $type
	 *
	 * @throws UnexpectedValueException
	 * @return AcliUserinterface
	 */
	public function displayMessage($message, $type = 'message')
	{
		$message =  implode("\n", (array) $message);

		switch($type)
		{
			case 'message':
				$gType = 'info';
				break;

			case 'warning':
				$gType = 'warning';
				break;

			case 'error':
				$gType = 'error';
				break;

			default:
				throw new UnexpectedValueException(__METHOD__ . ' - Invalid message type');
		}

		// Display the message
		shell_exec("zenity --$gType --text=\"$message\"");

		return $this;
	}
}
