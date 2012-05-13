<?php
/**
 * User: elkuku
 * Date: 12.05.12
 * Time: 18:56
 */
class JacliViewGetView extends JacliViewJson
{
	protected $appConfig = array();

	protected $appName = '';

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   12.1
	 * @throws  RuntimeException
	 */
	public function render()
	{
		/* @var JInput $input */
		$input = JFactory::getApplication()->input;

		$this->appName = $input->get('app');

		try
		{
			$item = $input->get('item');

			if( ! method_exists($this, $item))
				throw new RuntimeException(__METHOD__ . ' - Invalid do action: ' . $item);

			$this->$item();
		}
		catch (Exception $e)
		{
			$this->debug = $e->getMessage();
			$this->status = $e->getCode() ? : 1;
		}

		return parent::render();
	}

	private function appconfig()
	{
		$this->appConfig = JacliApplicationHelper::parseAppConfig($this->appName);

		$this->setLayout('appconfig');
	}
}
