<?php
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 16:21
 */

/**
 *
 */
class AcliModelDatabase extends JModelBase
{
	private $sql;

	private $connection;

	private $tablePrefix = '';

	private $cursor = null;

	/**
	 * @var JRegistry
	 */
	private $options = null;

	//private $dbDriver = null;

	public function __construct(JRegistry $options)
	{
		$this->connection = mysql_connect($options->get('db_host'), $options->get('db_user'), $options->get('db_pass'));

		/*
		$dbOptions = array(
			'driver' => $options->get('db_type'),
		);
		$this->dbDriver = JDatabaseDriver::getInstance($options->toArray());
		*/

		if (!$this->connection)
			throw new Exception('Could not connect: ' . mysql_error(), 1);

		$this->tablePrefix = $options->get('db_prefix');

		$this->options = $options;
	}

	public function createDB()
	{
		$dbName = $this->options->get('db_name');

		if (!$dbName)
			throw new UnexpectedValueException(__METHOD__ . ' - Empty database name', 1);

		$result = mysql_list_dbs($this->connection);

		while ($row = mysql_fetch_object($result))
		{
			if ($dbName == $row->Database)
				throw new Exception(__METHOD__ . ' - The database ' . $dbName . ' already exists', 1);
		}

		if (!mysql_query('CREATE DATABASE ' . $dbName, $this->connection))
		{
			mysql_close($this->connection);

			throw new Exception(__METHOD__ . ' - Error creating database: ' . mysql_error(), 1);
		}

		$this->selectDb($dbName);

		return $this;
	}

	public function selectDb($dbName)
	{
		$db_selected = mysql_select_db($dbName, $this->connection);

		if (!$db_selected)
			throw new Exception(__METHOD__ . ' - Can\'t use ' . $dbName . ' --> ' . mysql_error(), 1);

		return $this;
	}

	/**
	 * Sets the SQL query string for later execution.
	 *
	 * @param string $query The SQL query.
	 *
	 * @return    AcliModelDatabase    This object to support chaining.
	 */
	public function setQuery($query)
	{
		$this->sql = $query;

		return $this;
	}

	/**
	 * Execute the query.
	 *
	 * @throws Exception
	 * @return    mixed    A database resource if successful, FALSE if not.
	 */
	public function execute()
	{
		if (!is_resource($this->connection))
			throw new Exception(__METHOD__ . ' - not connected');

		// Take a local copy so that we don't modify the original query and cause issues later
		$sql = $this->replacePrefix((string) $this->sql);

		$this->cursor = mysql_query($sql, $this->connection);

		if (!$this->cursor)
		{
			$errorNum = mysql_errno($this->connection);
			$errorMsg = mysql_error($this->connection) . " SQL=$sql";

			throw new Exception(__METHOD__ . ': ' . $errorNum . ' - ' . $errorMsg, 1);
		}

		return $this->cursor;
	}

	/**
	 * Method to import a database schema from a file.
	 *
	 * @param string $schema Path to the schema file.
	 *
	 * @throws Exception
	 *
	 * @return    boolean    True on success.
	 */
	public function populateDatabase($schema)
	{
		// Get the contents of the schema file.
		$buffer = file_get_contents($schema);

		if (!$buffer)
			throw new Exception(__METHOD__ . ' - Unable to read the file ' . $schema, 1);

		// Get an array of queries from the schema and process them.
		$queries = $this->splitQueries($buffer);

		foreach ($queries as $query)
		{
			// Trim any whitespace.
			$query = trim($query);

			// If the query isn't empty and is not a comment, execute it.
			if (empty($query) || ($query{0} == '#'))
				continue;

			// Execute the query.
			$this->setQuery($query)
				->execute();
		}

		return $this;
	}

	public function quote($text)
	{
		$result = mysql_real_escape_string($text, $this->connection);
		$result = addcslashes($result, '%_');

		return '\'' . $result . '\'';
	}

	/**
	 * Method to split up queries from a schema file into an array.
	 *
	 * @param string $sql SQL schema.
	 *
	 * @return    array    Queries to perform.
	 */
	private function splitQueries($sql)
	{
		//return JDatabaseDriver::splitSql($sql);

		// Initialise variables.
		$buffer = array();
		$queries = array();
		$in_string = false;

		// Trim any whitespace.
		$sql = trim($sql);

		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n" . $sql);

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i++)
		{
			if ($sql[$i] == ";" && !$in_string)
			{
				$queries[] = substr($sql, 0, $i);
				$sql = substr($sql, $i + 1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
			{
				$in_string = false;
			}
			else if (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'")
				&& (!isset($buffer[0]) || $buffer[0] != "\\")
			)
			{
				$in_string = $sql[$i];
			}

			if (isset ($buffer[1]))
				$buffer[0] = $buffer[1];

			$buffer[1] = $sql[$i];
		}

		// If the is anything left over, add it to the queries.
		if (!empty($sql))
			$queries[] = $sql;

		return $queries;
	}

	/**
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>tablePrefix</var> class variable.
	 *
	 * @param  string  $sql     The SQL query
	 * @param  string  $prefix  The common table prefix
	 *
	 * @return string
	 */
	private function replacePrefix($sql, $prefix = '#__')
	{
		$sql = trim($sql);

		$escaped = false;
		$quoteChar = '';

		$n = strlen($sql);

		$startPos = 0;
		$literal = '';

		while ($startPos < $n)
		{
			$ip = strpos($sql, $prefix, $startPos);

			if ($ip === false)
				break;

			$j = strpos($sql, "'", $startPos);
			$k = strpos($sql, '"', $startPos);

			if (($k !== FALSE) && (($k < $j) || ($j === FALSE)))
			{
				$quoteChar = '"';
				$j = $k;
			}
			else
			{
				$quoteChar = "'";
			}

			if ($j === false)
				$j = $n;

			$literal .= str_replace($prefix, $this->tablePrefix
				, substr($sql, $startPos, $j - $startPos));

			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n)
				break;

			// quote comes first, find end of quote
			while (true)
			{
				$k = strpos($sql, $quoteChar, $j);
				$escaped = false;

				if ($k === false)
					break;

				$l = $k - 1;

				while ($l >= 0 && $sql{$l} == '\\')
				{
					$l--;
					$escaped = !$escaped;
				}

				if ($escaped)
				{
					$j = $k + 1;
					continue;
				}

				break;
			}

			// error in the query - no end quote; ignore it
			if ($k === false)
				break;

			$literal .= substr($sql, $startPos, $k - $startPos + 1);
			$startPos = $k + 1;
		}

		if ($startPos < $n)
			$literal .= substr($sql, $startPos, $n - $startPos);

		return $literal;
	}

}
