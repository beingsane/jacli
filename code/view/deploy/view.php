<?php
/**
 * User: elkuku
 * Date: 12.05.12
 * Time: 13:31
 */

class JacliViewDeployView extends JViewHtml
{
	protected $lists = array();

	protected $cfg = array();

	public function render()
	{
		$apps = array(array('items' => array('Select...')));

		foreach (JacliApplicationHelper::getApplicationList() as $afile => $app)
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

		/* @var JacliApplicationWeb $application */
		$application = JFactory::getApplication();

		foreach ($application->getConfig() as $k => $v)
		{
			if (in_array($k, array('application', 'version', 'target', 'execution', 'uri', 'interface')))
				continue;

			$this->cfg[$k] = $v;
		}

		return parent::render();
	}
}
