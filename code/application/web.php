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
		$this->config->set('target', $this->input->get('target'));
//var_dump(AcliApplicationHelper::getApplicationList($this->config));
		ob_start();

		include __DIR__ . '/tpl/deployoptions.php';

		$html = ob_get_clean();

		$this->appendBody($html);
	}

	protected function fetchConfigurationData($file = '', $class = 'JConfig')
	{
		return AcliApplicationHelper::fetchConfigurationData();
	}

	public function out($text = '', $nl = true)
	{
		echo ($nl) ? $text.'<br />' : $text;
	}
}
