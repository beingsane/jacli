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
	public $httpBase = 'http://joomla.tests';

	public $patchDir = '/home/elkuku/stormspace/jacli/patches';

	public $patches = array(
		'joomla-1.6--1/admin_autologin.patch',
		// 'error_reporting_-1',
	);


}
