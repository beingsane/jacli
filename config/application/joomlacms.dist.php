<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 17:55
 */

class AcliConfigJoomlacms extends JConfig
{
	// Database credentials
	public $db_type = 'mysqli';
	public $db_host = 'localhost';
	public $db_user = 'root';
	public $db_pass = '';
	public $db_prefix = 'joomla_';

	// Admin user credentials
	public $admin_user = 'admin';
	public $admin_password = 'test';
	public $admin_email = 'demo@example.com';

	// http://localhost
	public $httpBase = 'http://localhost';

	public $patchDir = '';

	public $patches = array(
		// 'patchfile',
	);
}
