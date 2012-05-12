<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 17:47
 */

/**
 * A KDE interface.
 */
class JacliUserinterfaceKde extends JacliUserinterface
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
				return shell_exec('kdialog --inputbox "' . $message . '"');

				break;

			case 'mchoice':
				$vs = '';

				foreach ($values as $value)
				{
					$vs .= " $value \"$value\"";
				}

				return shell_exec('kdialog --menu "' . $message . '" ' . $vs);

				break;

			case 'yesno':
				system('kdialog --yesno "' . $message . '"', $ret);

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
	 * @return JacliUserinterface
	 */
	public function displayMessage($message, $type = 'message')
	{
		$message =  implode("\n", (array) $message);

		switch($type)
		{
			case 'message':
				$kType = 'msgbox';
				break;

			case 'warning':
				$kType = 'sorry';
				break;

			case 'error':
				$kType = 'error';
				break;

			default:
				throw new UnexpectedValueException(__METHOD__ . ' - Invalid message type');
		}

		// Display the message
		shell_exec('kdialog --'.$kType.' "' . $message . '"');

		return $this;
	}
}
