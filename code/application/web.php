<?php defined('_JEXEC') || die('=;)');
/**
 * @package    JaCLI
 * @subpackage Base
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 01-May-2012
 * @license    GNU/GPL
 */

/**
 * An example JApplicationWeb application class.
 *
 * This example shows how to use the setBody and appendBody methods,
 * as well as access the client information.
 *
 * @package  JACLI
 */
class /*J*/AcliApplicationWeb extends JApplicationWeb
{
	protected $lists = array();

	protected $cfg = array();

	/**
	 * Overrides the parent doExecute method to run the web application.
	 *
	 * This method should include your custom code that runs the application.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function doExecute()
	{
		$do = $this->input->get('do');

		switch ($do)
		{
			case 'getAppConfig':
				$resp = new stdClass;
				$resp->text = AcliApplicationHelper::parseAppConfig($this->input->get('app'));
				$resp->status = 0;



				echo json_encode($resp);

				return;
				break;
		}

		$this->config->set('target', $this->input->get('target'));

		//---View start

		$apps = array(array('items' => array('Select...')));

		foreach (AcliApplicationHelper::getApplicationList() as $afile => $app)
		{
			$vs = array();

			foreach ($app->versions as $version)
			{
				$vs[$afile . '|' . $version->version] = (string) $version->version;
			}

			$apps[] = array('text' => $app->name, 'items' => $vs);
		}

		$options['list.attr'] = 'onchange="Jacli.changeApp(this, \'appConfig\');"';
		$this->lists['appversion'] = JHtml::_('select.groupedlist', $apps, 'appversion', $options);

		$this->cfg = array();

		foreach ($this->config->toObject() as $k => $v)
		{
			if (in_array($k, array('application', 'version', 'target', 'execution', 'uri',)))
				continue;

			$this->cfg[$k] = $v;
		}

		//---View end

		ob_start();

		include JACLI_PATH_TEMPLATE . '/default.php';

		$html = ob_get_clean();

		$this->appendBody($html);
	}

	/**
	 * Output a string.
	 *
	 * @param string $text
	 * @param bool $nl
	 */
	public function out($text = '', $nl = true)
	{
		echo ($nl) ? $text . '<br />' : $text;
	}

	/**
	 * Get a value from the user using the given user interface.
	 *
	 * @param        $message
	 * @param string $type
	 * @param array  $values
	 * @param bool   $required
	 *
	 * @return mixed|string
	 * @throws Exception
	 */
	public function getUserInput($message, $type = 'text', array $values = array(), $required = true)
	{
		return __METHOD__ . '---Missing value---' . $message;
		$retVal = $this->userInterface->getUserInput($message, $type, $values);

		$retVal = trim($retVal);

		if (!$retVal && $required)
			throw new Exception('User abort', 666);

		return $retVal;
	}

	/**
	 * Fetch the configuration data for the application.
	 *
	 * @param string $file
	 * @param string $class
	 *
	 * @throws RuntimeException
	 * @return  object  An object to be loaded into the application configuration.
	 *
	 * @since   1.0
	 */
	protected function fetchConfigurationData($file = '', $class = 'JConfig')
	{
		return AcliApplicationHelper::fetchConfigurationData($this->input->get('application'));
	}

}
