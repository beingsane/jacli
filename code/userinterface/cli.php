<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 17:47
 */

/**
 * A CLI interface.
 */
class JacliUserinterfaceCli extends JacliUserinterface
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
				$this->application->out()->out($message . ' ', false);

				return $this->application->in();

				break;

			case 'mchoice':
				$this->application->out()
					->output($message, true, 'yellow', '', 'bold')
					->out();

				foreach ($values as $i => $value)
				{
					$this->application->out(($i + 1).') '.$value);
				}

				$this->application->out()
					->output('Select: ', false, '', '', 'bold');

				$ret = (int) $this->application->in();

				return ($ret) ? isset($values[$ret - 1]) ? $values[$ret - 1] : false : false;

				break;

			case 'yesno':
				$this->application->out()
				->output($message, false, '', '', 'bold')
					->output(' [y/n]');

				$resp = $this->application->in();

				switch($resp)
				{
					case 'y':
						return 0;
						break;

					case 'n':
						return 1;
						break;

					default:
						$this->application->out('Please select either [y]es or [n]no');

						// recourse...
						return $this->getUserInput($message, $type, $values);
				}

				break;
		}

		throw new UnexpectedValueException(__METHOD__ . ' - Invalid input type:' . $type);
	}

	/**
	 * Display an error message.
	 *
	 * @param mixed $message array or string
	 * @param string $type
	 *
	 * @throws UnexpectedValueException
	 *
	 * @return JacliUserinterface
	 */
	public function displayMessage($message, $type = 'message')
	{
		$nl = "\n";

		$message = implode($nl, (array) $message);

		switch($type)
		{
			case 'message':
				$message = $nl.str_repeat('=', 60).$nl
				.$message
				.$nl.str_repeat('=', 60).$nl;

				$this->application->out()
					->output($message, true, '', '', 'bold')
					->out();
				break;

			case 'warning':
				$this->application->out()
					->output('Warning: ', false, 'yellow', '', 'bold')
					->output($message)
					->out();
				break;

			case 'error':
				$this->application->out()
					->output('Error: ', false, 'red', '', 'bold')
					->output($message)
					->out();
				break;

			default:
				throw new UnexpectedValueException(__METHOD__ . ' - Invalid message type');
		}

		return $this;
	}
}
