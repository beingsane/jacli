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
		$response = new stdClass;
		$response->text = $this->text . parent::render();
		$response->debug = $this->debug;
		$response->status = $this->status;

		echo json_encode($response);
	}
}
