<?php

namespace Datasource;

use PDO;
use Datetime;

class Connection{

	public $dbh;
	public $stmt;
	
	private $query;
	private $bindValues = [];
	private $info;

	public function __construct($conn)
	{

		$connString = $conn['type'] . ":host=" . $conn['host'] . ";dbname=" . $conn['dbname'] . ";charset=" . $conn['charset'];
		try {
			$dbh = new PDO($connString, $conn['username'], $conn['password']);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbh = $dbh;	
		} catch (Exception $e) {
			echo $e->getMessage();
			throw new Exception("Error", 1);
		}
	}

	public function rawQuery($query)
	{
		$this->query = $query;
		return $this;
	}
	public function go($data = [])
	{

		$this->stmt = $this->dbh->prepare($this->query);
		$data = array_merge($this->bindValues);
		if ($data) {
			$this->bind($data);
		}
		
		$this->stmt->execute();

		$this->info['query'] = $this->query;
		$this->query = '';

		$this->info['bindValues'] = $this->bindValues;
		$this->bindValues = [];

		return $this;
	}
	public function all($fetchType)
	{
		return $this->stmt->fetchAll(self::resolveFetch($fetchType));
	}
	public function first($fetchType)
	{
		return $this->stmt->fetch(self::resolveFetch($fetchType));
	}
	public function column()
	{
		return $this->stmt->fetchColumn();
	}
	protected static function resolveFetch($type)
	{
		switch ($type) {
			case 'assoc':
				$type = PDO::FETCH_ASSOC;
				break;
			case 'obj':
				$type = PDO::FETCH_OBJ;
				break;
		}

		return $type;
	}
	public function insertInto($table)
	{

		$this->query = "INSERT INTO {$table}";
		
		return $this;
	}
	public function values($values, $allowedFields = [])
	{
		$fields = [];
		$placeholders = [];

		foreach ($values as $key => $value) {
			if (!$allowedFields || in_array($key, $allowedFields)) {
				$fields[] = $key;
				$placeholders[] = ':' . $key;
				$this->bindValues[$key] = $value;
			}
		}
		$fields = join($fields, ', ');
		$placeholders = join($placeholders, ', ');

		$this->query .= " ({$fields}) VALUES ({$placeholders})";

		return $this;
	}

	public function deleteFrom($table)
	{
		$this->query = "DELETE FROM {$table}";

		return $this;
	}
	public function where($key, $value)
	{
		$where = $key . ' = :' . $key;

		$this->bindValues[$key] = $value;

		$this->query .= " WHERE {$where}";
		return $this;
	}
	public function update($table)
	{
		$this->query = "UPDATE {$table}";

		return $this;
	}
	public function set($data, $allowedFields = [])
	{
		$fields = [];
		foreach ($data as $placeholder => $value) {
			if (!$allowedFields || in_array($placeholder, $allowedFields)) {
				$fields[] = $placeholder .' = :'. $placeholder;
				$this->bindValues[$placeholder] = $value;
			}
		}
		$fields = join($fields, ', ');

		$this->query .= " SET {$fields}";

		return $this;
	}

	public function bind($data)
	{
		foreach ($data as $key => $value) {
			$this->stmt->bindValue(':' . $key, $value);
		}
		
		return $this;
	}

	public static function now()
	{
		return (new Datetime)->format('Y-m-d H:i:s');
	}
	/**
	 * GETTERS
	 */
	public function bindValues()
	{
		return $this->bindValues;
	}
	public function query()
	{
		return $this->query;
	}
	public function info()
	{
		return $this->info;
	}
	public function rowsAffected()
	{
		return $this->stmt->rowCount();
	}
	public function lastInsertId()
	{
		return $this->dbh->lastInsertId();
	}
}

?>