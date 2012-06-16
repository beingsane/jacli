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
class JacliApplicationWeb extends JApplicationWeb
{
	protected $lists = array();

	protected $cfg = array();

	/**
	 * Overrides the parent doExecute method to run the web application.
	 *
	 * This method should include your custom code that runs the application.
	 *
	 * @throws RuntimeException
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function doExecute()
	{
		// Get the controller instance based on the request.
		$controller = $this->fetchController();
		$model = $this->fetchModel();

		// Execute the controller.
		$controller->execute();

		$this->config->set('target', $this->input->get('target'));

		$viewName = $this->input->get('view', $this->input->get('do', 'default'));

		if( ! $viewName)
			throw new RuntimeException(__METHOD__.' - No view');

		$className = 'JacliView' . ucfirst($viewName).'View';

		$layouts = new SplPriorityQueue;
		$layouts->insert(JPATH_BASE.'/view/'.$viewName.'/tmpl', 0);

		/* @var JViewHtml $view */
		$view = new $className($model, $layouts);

		$output = $view->render();

		if ('get' == $viewName)
		{
			// This is a JSON output

			echo $output;

			return;
		}

		ob_start();

		include JACLI_PATH_TEMPLATE . '/default.php';

		$html = ob_get_clean();

		$html = str_replace('<!-- JacliApplicationOutput -->', $output, $html);

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
	}

    /**
     * Fetch the configuration data for the application.
     *
     * @param string $file
     * @param string $class
     *
     * @return mixed|object An object to be loaded into the application configuration.@since   1.0
     */
	protected function fetchConfigurationData($file = '', $class = 'JConfig')
	{
		return JacliApplicationHelper::fetchConfigurationData($this->input->get('application'));
	}

	/**
	 * Method to get a controller object based on the command line input.
	 *
	 * @return  JControllerBase
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function fetchController()
	{
		$base = 'JacliControllerweb';

		$sub = strtolower($this->input->get('do', 'default'));

		$className = $base . ucfirst($sub);

		// If the requested controller exists let's use it.
		if (class_exists($className))
		{
			return new $className($this->input, $this);
		}

		// Nothing found. Panic.
		throw new InvalidArgumentException('Controller not found: ' . $sub, 400);
	}

	/**
	 * Method to get a controller object based on the command line input.
	 *
	 * @return  JControllerBase
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	protected function fetchModel()
	{
		$base = 'JacliModel';

		$sub = strtolower($this->input->get('do', 'default'));

		//	$sub = $this->input->args[0];

		$className = $base . ucfirst($sub);

		// If the requested controller exists let's use it.
		if (class_exists($className))
		{
			return new $className;//($this->input, $this);
		}

		// Nothing found. Don't Panic.
		return new JacliModelDefault(new JRegistry);

		// Nothing found. Panic.
		throw new InvalidArgumentException('Model not found: ' . $sub, 400);
	}

	public function getConfig()
	{
		return $this->config->toObject();
	}

}
