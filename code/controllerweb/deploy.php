<?php
/**
 * User: elkuku
 * Date: 12.05.12
 * Time: 13:05
 */

class JacliControllerwebDeploy extends JControllerBase
{

	/**
	 * Execute the controller.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
	 *
	 * @since            12.1
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		if (JFactory::getApplication()->input->get('target'))
		{
			$model = new JacliModelDeploy(new JRegistry(JFactory::getApplication()->getConfig()));

			$model->deploy();
		}
	}
}
