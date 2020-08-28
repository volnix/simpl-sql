<?php

namespace Simpl;

class SQL
{
	/**
	 * @var \PDO
	 */
	public $pdo;

	/**
	 * SQL constructor.
	 * @param $host
	 * @param $db
	 * @param null $user
	 * @param null $pass
	 */
	public function __construct($host, $db, $user = null, $pass = null)
	{
		$charset = 'utf8mb4';

		if ($host == 'sqlite') {
			$dsn = "sqlite:$db";
		} else {
			$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		}

		$options = [
			\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES   => false,
		];

		try {
			$this->pdo = new \PDO($dsn, $user, $pass, $options);
		} catch (\PDOException $e) {
			throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
	}

	/**
	 * Used for SELECT commands.
	 * @param $sql
	 * @param array $params
	 * @return bool|false|\PDOStatement
	 */
	public function query($sql, $params = array())
	{
		if (empty($params)) {
			return $this->pdo->query($sql);
		} else {

			if (is_scalar($params)) {
				$params = array($params);
			}

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($params);
			return $stmt;
		}
	}

	/**
	 * @param $sql
	 * @param $params
	 * @param int $mode
	 * @return array
	 */
	public function fetchAll($sql, $params = array(), $mode = \PDO::FETCH_ASSOC)
	{
		if (empty($params)) {
			return $this->pdo->query($sql)->fetchAll($mode);
		} else {

			if (is_scalar($params)) {
				$params = array($params);
			}

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($params);
			return $stmt->fetchAll($mode);
		}
	}

	/**
	 * @param $sql
	 * @param $params
	 * @return array
	 */
	public function fetchColumn($sql, $params = array())
	{
		return $this->fetchAll($sql, $params, \PDO::FETCH_COLUMN);
	}

	/**
	 * Used for DELETE and UPDATE commands. Returns number of affected rows.
	 * @param $sql
	 * @param array $params
	 * @return int
	 */
	public function exec($sql, $params = array())
	{
		if (empty($params)) {
			return $this->pdo->exec($sql);
		} else {

			if (is_scalar($params)) {
				$params = array($params);
			}

			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($params);
			return $stmt->rowCount();
		}
	}

	/**
	 * Used for INSERT commands. Returns the last insert id.
	 * @param $sql
	 * @param array $params
	 * @return string
	 */
	public function insert($sql, $params = array())
	{
		$result = $this->exec($sql, $params);
		return $this->pdo->lastInsertId();
	}

	/**
	 * Build an "update" sql query from an array.
	 * @param string $table Name of table
	 * @param array $parts Key=>Value pairs
	 * @param string $condition Update condition, ie: "where name='Josh'"
	 * @return string sql string.
	 *
	 * This function escapes parameters with PDO::quote(), so they don't need to be
	 * quoted or escaped prior to sending thru this function.
	 */
	public function makeUpdate($table, $parts, $condition)
	{
		$sql = "update $table set ";

		foreach ($parts as $field=>$val) {
			$sql.="$field = " . $this->pdo->quote($val) . ", ";
		}

		$sql = preg_replace("/, $/", "", $sql);

		if($condition){
			$sql.=' where ' . $condition;
		}
		return $sql;
	}
	
	/**
	 * Build an "insert into" sql query from an array.
	 * @param string $table Name of table
	 * @param array $parts Key=>Value pairs
	 * @return string sql string.
	 *
	 * This function escapes parameters with PDO::quote(), so they don't need to be
	 * quoted or escaped prior to sending thru this function.
	 */
	public function makeInsert($table, $parts)
	{
		$sql = "insert into $table (";
		$sql2 = '' ;

		foreach ($parts as $field=>$val) {
			$sql.=$field . ', ';

			if ($val === null) {
				$sql2.= 'NULL' . ", ";
			} else {
				$sql2.= $this->pdo->quote($val) . ", ";
			}
		}
		$sql = preg_replace("/, $/", "", $sql);
		$sql2 = preg_replace("/, $/", "", $sql2);
		return $sql . ')values(' . $sql2 . ')';
	}

	/**
	 * Build a "replace into" sql query from an array.
	 * @param string $table Name of table
	 * @param array $parts Key=>Value pairs
	 * @return string sql string.
	 *
	 * This function escapes parameters with PDO::quote(), so they don't need to be
	 * quoted or escaped prior to sending thru this function.
	 */
	public function makeReplace($table, $parts)
	{
		$sql = "replace into $table (";
		$sql2 = '';

		foreach ($parts as $field=>$val) {
			$sql.=$field . ', ';

			if ($val === null) {
				$sql2.= 'NULL' . ", ";
			} else {
				$sql2.= $this->pdo->quote($val) . ", ";
			}
		}
		$sql = preg_replace("/, $/", "", $sql);
		$sql2 = preg_replace("/, $/", "", $sql2);
		return $sql . ')values(' . $sql2 . ')';
	}
}