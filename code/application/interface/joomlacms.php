<?php
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 19:55
 */
class JacliApplicationInterfaceJoomlacms extends JacliApplicationInterface
{
	protected $name = 'joomla-cms';

	/**
	 * Create the admin user.
	 *
	 * @param JacliModelDatabase $db
	 *
	 * @return JacliApplicationInterfaceJoomlacms
	 */
	public function createAdminUser(JacliModelDatabase $db)
	{
		// TODO: Implement createAdminUser() method.
		// Create random salt/password for the admin user
		$salt = self::genRandomPassword(32);
		$crypt = md5($this->config->get('admin_password') . $salt);
		$cryptpass = $crypt . ':' . $salt;

		// create the admin user
		date_default_timezone_set('UTC');

		$installdate = date('Y-m-d H:i:s');
		$nullDate = '0000-00-00 00:00:00';

		$query = 'REPLACE INTO #__users SET'
			. ' id = 42'
			. ', name = ' . $db->quote('Super User')
			. ', username = ' . $db->quote($this->config->get('admin_user'))
			. ', email = ' . $db->quote($this->config->get('admin_email'))
			. ', password = ' . $db->quote($cryptpass)
			. ', usertype = ' . $db->quote('deprecated') // Need to weed out where this is used
			. ', block = 0'
			. ', sendEmail = 1'
			. ', registerDate = ' . $db->quote($installdate)
			. ', lastvisitDate = ' . $db->quote($nullDate)
			. ', activation = ' . $db->quote('')
			. ', params = ' . $db->quote('');

		$db->setQuery($query)->execute();

		// Map the super admin to the Super Admin Group
		$query = 'REPLACE INTO #__user_usergroup_map'
			. ' SET user_id = 42, group_id = 8';

		$db->setQuery($query)
			->execute();

		return $this;
	}

	/**
	 * @return \JacliApplicationInterfaceJoomlacms
	 * @throws Exception
	 */
	public function createConfig()
	{
		$path = $this->config->get('targetDir');
		// Create a new registry to build the configuration options.
		$registry = new stdClass;

		/* Site Settings */
		$registry->offline = 0;
		$registry->offline_message = 'Offline...'; // JText::_('INSTL_STD_OFFLINE_MSG');
		$registry->sitename = $this->config->get('site_name');
		$registry->editor = 'tinymce';
		$registry->list_limit = 20;
		$registry->access = 1;

		/* Debug Settings */
		$registry->debug = 0;
		$registry->debug_lang = 0;

		/* Database Settings */
		$registry->dbtype = $this->config->get('db_type');
		$registry->host = $this->config->get('db_host');
		$registry->user = $this->config->get('db_user');
		$registry->password = $this->config->get('db_pass');
		$registry->db = $this->config->get('db_name');
		$registry->dbprefix = $this->config->get('db_prefix');

		/* Server Settings */
		$registry->live_site = '';
		$registry->secret = self::genRandomPassword(16);
		$registry->gzip = 0;
		$registry->error_reporting = -1;
		$registry->helpurl = 'http://help.joomla.org/proxy/index.php'
			. '?option=com_help&amp;keyref=Help{major}{minor}:{keyref}';
		$registry->ftp_host = ''; //$options->ftp_host;
		$registry->ftp_port = ''; //$options->ftp_port;
		$registry->ftp_user = ''; //$options->ftp_save ? $options->ftp_user : '';
		$registry->ftp_pass = ''; //$options->ftp_save ? $options->ftp_pass : '';
		$registry->ftp_root = ''; //$options->ftp_save ? $options->ftp_root : '';
		$registry->ftp_enable = '0'; //$options->ftp_enable;

		/* Locale Settings */
		$registry->offset = 'UTC';
		$registry->offset_user = 'UTC';

		/* Mail Settings */
		$registry->mailer = 'mail';
		$registry->mailfrom = $this->config->get('admin_email');
		$registry->fromname = $this->config->get('site_name');
		$registry->sendmail = '/usr/sbin/sendmail';
		$registry->smtpauth = 0;
		$registry->smtpuser = '';
		$registry->smtppass = '';
		$registry->smtphost = 'localhost';
		$registry->smtpsecure = 'none';
		$registry->smtpport = '25';

		/* Cache Settings */
		$registry->caching = 0;
		$registry->cache_handler = 'file';
		$registry->cachetime = 15;

		/* Meta Settings */
		$registry->MetaDesc = ''; //$options->site_metadesc;
		$registry->MetaKeys = ''; //$options->site_metakeys;
		$registry->MetaTitle = 1;
		$registry->MetaAuthor = 1;

		/* SEO Settings */
		$registry->sef = 1;
		$registry->sef_rewrite = 0;
		$registry->sef_suffix = 0;
		$registry->unicodeslugs = 0;

		/* Feed Settings */
		$registry->feed_limit = 10;
		$registry->log_path = $path . DIRECTORY_SEPARATOR . 'logs';
		$registry->tmp_path = $path . DIRECTORY_SEPARATOR . 'tmp';

		/* Session Setting */
		$registry->lifetime = 15;
		$registry->session_handler = 'database';

		$buffer = self::objectToString($registry);

		// Build the configuration file path.
		if (!file_put_contents($path . DIRECTORY_SEPARATOR . 'configuration.php', $buffer))
			throw new Exception('Unable to write configuration.php', 1);

		return $this;
	}

	/**
	 * Converts an object into a php class string.
	 *    - NOTE: Only one depth level is supported.
	 *
	 * @param stdClass $object
	 *
	 * @internal param \Data $object Source Object
	 *
	 * @return    string    Config class formatted string
	 */
	private static function objectToString(stdClass $object)
	{
		$str = "<?php\nclass JConfig {\n";

		foreach (get_object_vars($object) as $k => $v)
		{
			if (is_scalar($v))
			{
				$str .= "\tpublic $" . $k . " = '" . addcslashes($v, '\\\'') . "';\n";
			}
			else if (is_array($v))
			{
				$str .= "\tpublic $" . $k . " = " . self::getArrayString($v) . ";\n";
			}
		}

		$str .= '}';

		return $str;
	}

	/**
	 * @static
	 *
	 * @param array $a
	 *
	 * @return string
	 */
	private static function getArrayString(array $a)
	{
		$s = 'array(';
		$i = 0;

		foreach ($a as $k => $v)
		{
			$s .= ($i) ? ', ' : '';
			$s .= '"' . $k . '" => ';

			if (is_array($v))
			{
				$s .= self::getArrayString($v);
			}
			else
			{
				$s .= '"' . addslashes($v) . '"';
			}

			$i++;
		}

		$s .= ')';

		return $s;
	}

	/**
	 * Generate a random password.
	 *
	 * @param    int        $length    Length of the password to generate
	 *
	 * @return    string            Random Password
	 */
	private static function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$makepass = '';

		$stat = @stat(__FILE__);

		if (empty($stat) || !is_array($stat))
			$stat = array(php_uname());

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i++)
		{
			$makepass .= $salt[mt_rand(0, $len - 1)];
		}

		return $makepass;
	}


	public function cleanup()
	{
		// TODO: Implement cleanup() method.
		$this->out('Deleting installation directory...', false);

		if (!JFolder::delete($this->config->get('targetDir') . '/installation'))
			throw new Exception(JError::getError(), 1);

		$this->out('ok');

		return $this;
	}

	public function setupDatabase()
	{
		$installSql = $this->config->get('targetDir') . '/installation/sql/mysql/joomla.sql';

		if (!file_exists($installSql))
			throw new Exception(__METHOD__ . ' - Install SQL file not found in ' . $installSql, 1);

		$this->config->set('site_name', 'TEST ' . $this->config->get('target'));
		$this->config->set('db_name', $this->config->get('target'));

		$dbModel = new JacliModelDatabase($this->config);

		$this->out(sprintf('Creating database %s ...', $this->config->get('db_name')), false);
		$dbModel->createDB();
		$this->out('ok');

		$this->out('Populating database...', false);
		$dbModel->populateDatabase($installSql);
		$this->out('ok');

		$this->out('Creating admin user...', false);
		$this->createAdminUser($dbModel);
		$this->out('ok');

		return $this;
	}

	public function getBrowserLinks()
	{
		return array(
			'Site' => '',
			'Administrator' => '/administrator');
	}

	/**
	 * Displays a result message.
	 *
	 * @return array
	 */
	public function getResultMessage()
	{
		// TODO: Implement displayResult() method.

		$message = array();

		$message[] = '';
		$message[] = 'Your Joomla! CMS has been installed succesfully.';
		$message[] = '';
		$message[] = 'Credentials:';
		$message[] = 'Admin user     : ' . $this->config->get('admin_user');
		$message[] = 'Admin password : ' . $this->config->get('admin_password');
		$message[] = '';

		return $message;
	}
}
