<?php
/**
 * User: elkuku
 * Date: 12.05.12
 * Time: 18:52
 */

class JacliViewJson extends JViewHtml
{
	protected $text = '';

	protected $debug = '';

	protected $status = 0;

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
		$resp = new stdClass;
		$resp->text = $this->text . parent::render();
		$resp->debug = $this->debug;
		$resp->status = $this->status;

		echo json_encode($resp);
	}
}
