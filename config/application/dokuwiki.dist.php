<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 18:34
 */

class AcliConfigDokuwiki extends JConfig
{
	public $lang = 'en';
	public $license = 0;
	public $useacl = 1;
	public $policy = 0;

	// Admin user credentials
	public $admin_user = 'admin';
	public $admin_fullname = 'The Admin';
	public $admin_password = 'test';
	public $admin_email = 'demo@example.com';

	public $httpBase = 'http://joomla.tests';

	public $patchDir = '';

	public $patches = array();

}
