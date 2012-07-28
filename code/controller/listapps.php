<?php
/**
 * User: elkuku
 * Date: 11.05.12
 * Time: 11:36
 */
class JacliControllerListapps extends JControllerBase
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
		$model = new JacliModelTargetapp;

		$model->listApplications();
	}
}
