<?php
class Datasource{

	public $dbh;
	public $stmt;
	public $query;

	public function __construct($conn)
	{

		$connString = $conn['type'] . ":host=" . $conn['host'] . ";dbname=" . $conn['dbname'] . ";charset=" . $conn['charset'];

		$dbh = new PDO($connString, $conn['username'], $conn['password']);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->dbh = $dbh;
	}

	public function rawQuery($query)
	{
		$this->query = $query;
		$this->stmt = $this->dbh->prepare($this->query);
		return $this;
	}
	public function execute()
	{
		$this->stmt->execute();
		return $this;
	}
	public function all($fetchType)
	{
		$this->stmt->execute();
		return $this->stmt->fetchAll(self::resolveFetch($fetchType));
	}
	public function first($fetchType)
	{
		$this->stmt->execute();
		return $this->stmt->fetch(self::resolveFetch($fetchType));
	}
	public function column()
	{
		$this->stmt->execute();
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
	public function insert($table, $data)
	{
		
		$fields = [];
		$placeholders = [];
		foreach ($data as $key => $value) {
			$fields[] = $key;
			$placeholders[] = ':' . $key;
		}
		$fields = join($fields, ', ');
		$placeholders = join($placeholders, ', ');

		$this->query = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
		$this->stmt = $this->dbh->prepare($this->query);
		$this
			->bind($data)
			->execute();
	}
	public function delete($table, $conditions)
	{
		$data = [];
		foreach ($conditions as $key => $condition) {
			$where[] = $key . '= :' .$key;
			$data[$key] = $condition;
		}
		$where = join($where, ' AND ');

		$this->query = "DELETE FROM {$table} WHERE {$where}";

		$this->stmt = $this->dbh->prepare($this->query);
		$this
			->bind($data)
			->execute();
	}
	public function update($table, $data, $conditions)
	{
		
		$fields = [];
		foreach ($data as $placeholder => $value) {
			$fields[] = $placeholder .' = :'. $placeholder;
		}
		$fields = join($fields, ', ');

		$where = [];
		foreach ($conditions as $key => $condition) {
			$where[] = $key . ' = :' .$key;
			$data[$key] = $condition;
		}
		$where = join($where, ' AND ');

		$this->query = "UPDATE {$table} SET {$fields} WHERE {$where}";
		
		$this->stmt = $this->dbh->prepare($this->query);
		$this
			->bind($data)
			->execute();
	}

	public function bind($data)
	{
		foreach ($data as $key => $value) {
			$this->stmt->bindValue(':' . $key, $value);
		}
		
		return $this;
	}
}

$conn = [
	'default' => [
		'type' => 'mysql',
		'host' => 'ppmy0040.servidorwebfacil.com',
		'dbname' => 'contato65_kotacao',
		'username' => 'conta_kotacao',
		'password' => 'sWeDu7rehEcu4a',
		'charset' => 'utf8',
	]
];

?>