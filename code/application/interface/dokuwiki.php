<?php
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 19:55
 */

class AcliApplicationInterfaceDokuwiki extends AcliApplicationInterface
{
	public function createAdminUser(AcliModelDatabase $db)
	{
		return $this;
	}

	public function createConfig()
	{
		jimport('joomla.filesystem.file');

		$path = $this->targetDir;

		$cfg = new stdClass;
		$cfg->title = $this->config->get('site_name');
		$cfg->lang = 'de';
		$cfg->license = 0;
		$cfg->useacl = 1;
		$cfg->policy = 0;
		//$cfg->htmlok = 1;

		$now = gmdate('r');
		$buffer = <<<EOT
<?php
/**
 * Dokuwiki's Main Configuration File - Local Settings
 * Auto-generated by install script
 * Date: $now
 */

EOT;
		$buffer .= '$conf[\'title\'] = \'' . addslashes($cfg->title) . "';\n";
		$buffer .= '$conf[\'lang\'] = \'' . addslashes($cfg->lang) . "';\n";
		$buffer .= '$conf[\'license\'] = \'' . addslashes($cfg->license) . "';\n";

		if ($cfg->useacl)
		{
			$buffer .= '$conf[\'useacl\'] = 1' . ";\n";
			$buffer .= "\$conf['superuser'] = '@admin';\n";
		}

		if (!file_put_contents($path . '/conf/local.php', $buffer))
			throw new Exception('Unable to write local.php', 1);

		/*
		 * Create admin user
		 */

		if (!$cfg->useacl)
			return $this;

		// create users.auth.php
		// --- user:MD5password:Real Name:email:groups,comma,seperated
		$output = join(":", array(
			$this->config->get('admin_user'),
			md5($this->config->get('admin_password')),
			$this->config->get('admin_fullname'),
			$this->config->get('admin_email'),
			'admin,user'
		));

		$buffer = JFile::read($path.'/conf/users.auth.php.dist') . "\n$output\n";

		if (!file_put_contents($path . '/conf/users.auth.php', $buffer))
			throw new Exception('Unable to write users.auth.php', 1);
//		$ok = $ok && fileWrite(DOKU_LOCAL . 'users.auth.php', $output);

		// create acl.auth.php
		$buffer = <<<EOT
# acl.auth.php
# <?php exit()?>
# Don't modify the lines above
#
# Access Control Lists
#
# Auto-generated by install script
# Date: $now

EOT;
		if ($cfg->policy == 2)
		{
			$buffer .= "*               @ALL          0\n";
			$buffer .= "*               @user         8\n";
		}
		elseif ($cfg->policy == 1)
		{
			$buffer .= "*               @ALL          1\n";
			$buffer .= "*               @user         8\n";
		}
		else
		{
			$buffer .= "*               @ALL          8\n";
		}

		//$ok = $ok && fileWrite(DOKU_LOCAL . 'acl.auth.php', $output);
		if (!file_put_contents($path . '/conf/acl.auth.php', $buffer))
			throw new Exception('Unable to write acl.auth.php', 1);

		return $this;
	}

	public function cleanup()
	{
		return $this;
	}

	public function setupDatabase()
	{
		return $this;
	}

	public function getBrowserLinks()
	{
		return array('Wiki' => '');
	}
}