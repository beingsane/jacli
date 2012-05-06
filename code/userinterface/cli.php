<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 17:47
 */

/**
 * A CLI interface.
 */
class AcliUserinterfaceCli extends AcliUserinterface
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
					->out($message);

				foreach ($values as $i => $value)
				{
					$this->application->out(($i + 1).') '.$value);
				}

				$this->application->out('Select: ', false);

				$ret = (int) $this->application->in();

				return ($ret) ? isset($values[$ret - 1]) ? $values[$ret - 1] : false : false;

				break;

			case 'yesno':
				$this->application->out($message . ' [y/n]');

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
						$this->getUserInput($message, $type, $values);
				}

				break;

			default:
				throw new UnexpectedValueException(__METHOD__ . ' - Invalid input type:' . $type);
		}
	}

	/**
	 * Display an error message.
	 *
	 * @param string $message
	 * @param string $type
	 *
	 * @throws UnexpectedValueException
	 *
	 * @return AcliUserinterface
	 */
	public function displayMessage($message, $type = 'message')
	{
		switch($type)
		{
			case 'message':
				$message = "\n".str_repeat('=', 60)."\n"
				.$message
				.str_repeat('=', 60)."\n";
				break;

			case 'warning':
				$message = '**** WARNING: '.$message;
				break;

			case 'error':
				$message = '**** ERROR: '.$message;
				break;

			default:
				throw new UnexpectedValueException(__METHOD__ . ' - Invalid message type');
		}

		$this->application->out($message);

		return $this;
	}
}
