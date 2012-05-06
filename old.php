<?php
/**
 * This is OLD CODE !!!
 *
 * It is kept here for reference only.
 *
 * @version SVN $Id: j_1.6_db_installer.php 494 2011-08-06 07:50:29Z elkuku $
 *
 * This script...
 * 1. Checks out the Joomla! trunk
 * 2. Exports it
 * 3. Performs the installation and
 * 4. Opens a browser window
 *
 * The script must be run from the command line.
 *
 * Modify the paths !! - and have Fun =;)
 */

if('cli' != php_sapi_name())
	die('This script must be executed from the command line');

define('NL', "\n");
define('DS', DIRECTORY_SEPARATOR);

/*
 *  Mofify from here ->>
 */
$browserExe = 'firefox';// C:\path\to\browser.exe
$httpBase = 'http://indigogit2.kuku';// http://localhost

$BASE = '/home/elkuku/eclipsespace/indigogit2';// C:\path\to\your\workspace
$jTrunkDir = 'joomla_trunk';// some_name

$patches = array();
$patches[] = 'admin_autologin';
//$patches[] = 'error_reporting_-1';

$patchDir = $BASE.'/elkuku_utilities/patches/1.6';

$options = new stdClass;

$options->db_type = 'mysqli';
$options->db_host = 'localhost';
$options->db_user = 'root';
$options->db_pass = '';
$options->db_prefix = 'kuku_';

$options->admin_user = 'admin';
$options->admin_password = 'test';
$options->admin_email = 'test@nik-it.de';
/*
 * <<- Modify until here
 */

echo 'Enter the directory to receive the export: ';
$theDir = trim(fgets(STDIN));

if( ! $theDir)
	exit('Bye bye...');

$theDir = str_replace(' ', '_', $theDir);

if(file_exists($BASE.DS.$theDir))
	die('The directory must not exist - aborting :(');

$options->site_name = 'TEST '.$theDir;
$options->db_name = $theDir;

/* START */

/*
 * SVN checkout
 */
echo 'Checking out the Joomla! trunk to:'.NL.$BASE.DS.$jTrunkDir.NL;
$JSVN = 'http://joomlacode.org/svn/joomla/development/trunk';
system('svn co '.$JSVN.' "'.$BASE.DS.$jTrunkDir.'"');

/*
 * SVN export
 */
echo "Exporting the Joomla! trunk to $theDir...";
system('svn export "'.$BASE.DS.$jTrunkDir.'" "'.$BASE.DS.$theDir.'"');

/*
 * DB install
 */
$installSql = $BASE.'/'.$theDir.'/installation/sql/mysql/joomla.sql';

if( ! file_exists($installSql))
	die('Install SQL file not found in '.$installSql);

$dbHelper = new dbHelper($options);

echo 'Create database '.$options->db_name.'...';
$dbHelper->createDB($options->db_name);
echo 'OK'.NL;

echo 'Populate database...';
$dbHelper->populateDatabase($installSql);
echo 'OK'.NL;

echo 'Create root user...';
$dbHelper->createRootUser($options);
echo 'OK'.NL;

/*
 * Create configuration.php
 */
echo 'Create configuration.php...';
configHelper::createConfig($options, $BASE.DS.$theDir);
echo 'OK'.NL;

/*
 * Applying patches
 */

echo 'Applying patches'.NL;

foreach($patches as $patch)
{
	echo 'Applying '.$patch.'...';

	$patchFile = $patchDir.DS.$patch.'.patch';

	if( ! file_exists($patchFile))
	{
		echo 'not found :('.NL;

		continue;
	}

	system("patch -d \"$BASE/$theDir\" -p0 < \"$patchFile\"");

	echo 'OK'.NL;
}//foreach

/*
 * Open in browser
 */
echo 'Open in browser: '.$browserExe.NL;
system($browserExe.' '.$httpBase.'/'.$theDir.' &');
system($browserExe.' '.$httpBase.'/'.$theDir.'/administrator &');

echo NL.'Finished =;)'.NL;

/**
 * Config class.
 */
class configHelper
{
	public static function createConfig($options, $path)
	{
		// Create a new registry to build the configuration options.
		$registry = new stdClass;

		/* Site Settings */
		$registry->offline = 0;
		$registry->offline_message = 'Offline...';// JText::_('INSTL_STD_OFFLINE_MSG');
		$registry->sitename = $options->site_name;
		$registry->editor = 'tinymce';
		$registry->list_limit = 20;
		$registry->access = 1;

		/* Debug Settings */
		$registry->debug = 0;
		$registry->debug_lang = 0;

		/* Database Settings */
		$registry->dbtype = $options->db_type;
		$registry->host = $options->db_host;
		$registry->user = $options->db_user;
		$registry->password = $options->db_pass;
		$registry->db = $options->db_name;
		$registry->dbprefix = $options->db_prefix;

		/* Server Settings */
		$registry->live_site = '';
		$registry->secret = passwordHelper::genRandomPassword(16);
		$registry->gzip = 0;
		$registry->error_reporting = -1;
		$registry->helpurl = 'http://help.joomla.org/proxy/index.php'
			.'?option=com_help&amp;keyref=Help{major}{minor}:{keyref}';
		$registry->ftp_host = '';//$options->ftp_host;
		$registry->ftp_port = '';//$options->ftp_port;
		$registry->ftp_user = '';//$options->ftp_save ? $options->ftp_user : '';
		$registry->ftp_pass = '';//$options->ftp_save ? $options->ftp_pass : '';
		$registry->ftp_root = '';//$options->ftp_save ? $options->ftp_root : '';
		$registry->ftp_enable = '0';//$options->ftp_enable;

		/* Locale Settings */
		$registry->offset = 'UTC';
		$registry->offset_user = 'UTC';

		/* Mail Settings */
		$registry->mailer = 'mail';
		$registry->mailfrom = $options->admin_email;
		$registry->fromname = $options->site_name;
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
		$registry->MetaDesc = '';//$options->site_metadesc;
		$registry->MetaKeys = '';//$options->site_metakeys;
		$registry->MetaTitle = 1;
		$registry->MetaAuthor = 1;

		/* SEO Settings */
		$registry->sef = 1;
		$registry->sef_rewrite = 0;
		$registry->sef_suffix = 0;
		$registry->unicodeslugs = 0;

		/* Feed Settings */
		$registry->feed_limit = 10;
		$registry->log_path = $path.DS.'logs';
		$registry->tmp_path = $path.DS.'tmp';

		/* Session Setting */
		$registry->lifetime = 15;
		$registry->session_handler = 'database';

		$buffer = self::objectToString($registry);

		// Build the configuration file path.
		$path .= DS.'configuration.php';

		if( ! file_put_contents($path, $buffer))
			die('Unable to write configuration.php');

		return true;
	}//function

	/**
	 * Converts an object into a php class string.
	 *	- NOTE: Only one depth level is supported.
	 *
	 * @param	object	Data Source Object
	 *
	 * @return	string	Config class formatted string
	 */
	private static function objectToString($object)
	{
		$str = '<?php'.NL.'class JConfig {'.NL;

		foreach(get_object_vars($object) as $k => $v)
		{
			if(is_scalar($v))
			{
				$str .= "\tpublic $".$k." = '".addcslashes($v, '\\\'')."';\n";
			}
			else if(is_array($v))
			{
				$str .= "\tpublic $".$k." = ".self::getArrayString($v).";\n";
			}
		}//foreach

		$str .= '}';

		return $str;
	}//function

	private static function getArrayString($a)
	{
		$s = 'array(';
		$i = 0;

		foreach($a as $k => $v)
		{
			$s .= ($i) ? ', ' : '';
			$s .= '"'.$k.'" => ';

			if(is_array($v))
			{
				$s .= self::getArrayString($v);
			}
			else//
			{
				$s .= '"'.addslashes($v).'"';
			}

			$i++;
		}//foreach

		$s .= ')';

		return $s;
	}//function
}//class

/**
 * Database helper class.
 */
class dbHelper extends JDatabase
{
	private $_sql;

	private $_connection;

	private $_table_prefix = '';

	public function __construct($options)
	{
		$this->_connection = mysql_connect($options->db_host, $options->db_user, $options->db_pass);

		if( ! $this->_connection)
			die('Could not connect: '.mysql_error());

		$this->_table_prefix = $options->db_prefix;
	}//function

	public function createDB($dbName)
	{
		$result = mysql_list_dbs($this->_connection);

		while($row = mysql_fetch_object($result))
		{
			if($dbName == $row->Database)
				die('The database '.$dbName.' already exists - aborting :('.NL);
		}//while

		if( ! mysql_query('CREATE DATABASE '.$dbName, $this->_connection))
		{
			mysql_close($this->_connection);

			die('Error creating database: '.mysql_error());
		}

		$this->selectDb($dbName);

		return true;
	}//function

	public function selectDb($dbName)
	{
		$db_selected = mysql_select_db($dbName, $this->_connection);

		if( ! $db_selected)
			die ('Can\'t use '.$dbName.' --> '.mysql_error());

		return true;
	}//function

	/**
	 * Sets the SQL query string for later execution.
	 *
	 * @param string $query The SQL query.
	 *
	 * @return	object	This object to support chaining.
	 */
	private function setQuery($query)
	{
		$this->_sql = $query;

		return $this;
	}//function

	/**
	 * Execute the query.
	 *
	 * @return	mixed	A database resource if successful, FALSE if not.
	 */
	private function query()
	{
		if( ! is_resource($this->_connection))
		{
			return false;
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string)$this->_sql);

		$this->_errorNum = 0;
		$this->_errorMsg = '';
		$this->_cursor = mysql_query($sql, $this->_connection);

		if( ! $this->_cursor)
		{
			$this->_errorNum = mysql_errno($this->_connection);
			$this->_errorMsg = mysql_error($this->_connection)." SQL=$sql";

			die('JDatabaseMySQL::query: '.$this->_errorNum.' - '.$this->_errorMsg);
		}

		return $this->_cursor;
	}//function

	/**
	 * Method to import a database schema from a file.
	 *
	 * @param	string	Path to the schema file.
	 *
	 * @return	boolean	True on success.
	 */
	public function populateDatabase($schema)
	{
		// Initialise variables.
		$return = true;

		// Get the contents of the schema file.
		if( ! ($buffer = file_get_contents($schema)))
		{
			die('Unable to read the file '.$schema);
		}

		// Get an array of queries from the schema and process them.
		$queries = $this->splitQueries($buffer);

		foreach($queries as $query)
		{
			// Trim any whitespace.
			$query = trim($query);

			// If the query isn't empty and is not a comment, execute it.
			if( ! empty($query) && ($query{0} != '#'))
			{
				// Execute the query.
				$this->setQuery($query);

				if( ! $this->query())
					$return = false;
			}
		}//foreach

		return $return;
	}//function

	public function createRootUser($options)
	{
		// Create random salt/password for the admin user
		$salt = passwordHelper::genRandomPassword(32);
		$crypt = passwordHelper::getCryptedPassword($options->admin_password, $salt);
		$cryptpass = $crypt.':'.$salt;

		// create the admin user
		date_default_timezone_set('UTC');

		$installdate = date('Y-m-d H:i:s');
		$nullDate = '0000-00-00 00:00:00';
		$query	= 'REPLACE INTO #__users SET'
			. ' id = 42'
			. ', name = '.$this->quote('Super User')
			. ', username = '.$this->quote($options->admin_user)
			. ', email = '.$this->quote($options->admin_email)
			. ', password = '.$this->quote($cryptpass)
			. ', usertype = '.$this->quote('deprecated')		// Need to weed out where this is used
			. ', block = 0'
			. ', sendEmail = 1'
			. ', registerDate = '.$this->quote($installdate)
			. ', lastvisitDate = '.$this->quote($nullDate)
			. ', activation = '.$this->quote('')
			. ', params = '.$this->quote('');

		$this->setQuery($query);

		if( ! $this->query())
			die(mysql_error($this->_connection));

		// Map the super admin to the Super Admin Group
		$query = 'REPLACE INTO #__user_usergroup_map'
			.' SET user_id = 42, group_id = 8';

		$this->setQuery($query);

		if( ! $this->query())
			die(mysql_error($this->_connection));

		return true;
	}//function

	private function quote($text)
	{
		$result = mysql_real_escape_string($text, $this->_connection);
		//		if ($extra) {
		$result = addcslashes($result, '%_');
		//		}
		return '\''.$result.'\'';
	}//function

	/**
	 * Method to split up queries from a schema file into an array.
	 *
	 * @param string $sql SQL schema.
	 *
	 * @return	array	Queries to perform.
	 */
	private function splitQueries($sql)
	{
		// Initialise variables.
		$buffer		= array();
		$queries	= array();
		$in_string	= false;

		// Trim any whitespace.
		$sql = trim($sql);

		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);

		// Parse the schema file to break up queries.
		for($i = 0; $i < strlen($sql) - 1; $i ++)
		{
			if($sql[$i] == ";" && !$in_string)
			{
				$queries[] = substr($sql, 0, $i);
				$sql = substr($sql, $i + 1);
				$i = 0;
			}

			if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
			{
				$in_string = false;
			}
			else if( ! $in_string && ($sql[$i] == '"' || $sql[$i] == "'")
				&& ( ! isset($buffer[0]) || $buffer[0] != "\\"))
			{
				$in_string = $sql[$i];
			}

			if(isset ($buffer[1]))
				$buffer[0] = $buffer[1];

			$buffer[1] = $sql[$i];
		}//for

		// If the is anything left over, add it to the queries.
		if( ! empty($sql))
			$queries[] = $sql;

		return $queries;
	}//function

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>tablePrefix</var> class variable.
	 *
	 * @param	string	The SQL query
	 * @param	string	The common table prefix
	 */
	private function replacePrefix($sql, $prefix = '#__')
	{
		$sql = trim($sql);

		$escaped = false;
		$quoteChar = '';

		$n = strlen($sql);

		$startPos = 0;
		$literal = '';

		while($startPos < $n)
		{
			$ip = strpos($sql, $prefix, $startPos);

			if($ip === false)
				break;

			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);

			if(($k !== FALSE) && (($k < $j) || ($j === FALSE)))
			{
				$quoteChar	= '"';
				$j			= $k;
			}
			else//
			{
				$quoteChar	= "'";
			}

			if($j === false)
				$j = $n;

			$literal .= str_replace($prefix, $this->_table_prefix
				, substr($sql, $startPos, $j - $startPos));

			$startPos = $j;

			$j = $startPos + 1;

			if($j >= $n)
				break;

			// quote comes first, find end of quote
			while(true)
			{
				$k = strpos($sql, $quoteChar, $j);
				$escaped = false;

				if($k === false)
					break;

				$l = $k - 1;

				while($l >= 0 && $sql{$l} == '\\')
				{
					$l--;
					$escaped = !$escaped;
				}//while

				if($escaped)
				{
					$j	= $k + 1;
					continue;
				}

				break;
			}//while

			// error in the query - no end quote; ignore it
			if($k === false)
				break;

			$literal .= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k + 1;
		}//while

		if($startPos < $n)
			$literal .= substr($sql, $startPos, $n - $startPos);

		return $literal;
	}//function
}//class

/**
 * Password helper class.
 *
 */
class passwordHelper
{
	/**
	 * Formats a password using the current encryption.
	 *
	 * @param string $plaintext The plaintext password to encrypt.
	 * @param string $salt The salt to use to encrypt the password. []
	 *
	 * @return string The encrypted password.
	 */
	public static function getCryptedPassword($plaintext, $salt = '')
	{
		return md5($plaintext.$salt);
	}//function

	/**
	 * Generate a random password.
	 *
	 * @param	int		$length	Length of the password to generate
	 *
	 * @return	string			Random Password
	 */
	public static function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$makepass = '';

		$stat = @stat(__FILE__);

		if(empty($stat)
			|| ! is_array($stat))
			$stat = array(php_uname());

		mt_srand(crc32(microtime().implode('|', $stat)));

		for($i = 0; $i < $length; $i ++)
		{
			$makepass .= $salt[mt_rand(0, $len - 1)];
		}//for

		return $makepass;
	}//function
}//class
