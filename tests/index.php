<?php

namespace Test;

require "../vendor/autoload.php";

use Datasource\Connection;

$config = [
	'default' => [
		'type' => 'mysql',
		'host' => 'localhost',
		'dbname' => 'blog',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8',
	]
];

$conn = new Connection($config['default']);

/**
 * SELECT
 */
$teste = $conn
	->rawQuery('SELECT * FROM articles')
	->go()
	->all('obj');

print_r($teste);

$data = ['title' => 'Dr. Dre'];

/**
 * INSERT
 */
$conn
	->insertInto('articles')
	->values($data)
	->go();

/**
 * UPDATE
 */
$conn
	->update('articles')
	->set($data)
	->where('id', 132)
	->go();

/**
 * DELETE
 */
$conn
	->deleteFrom('articles')
	->where('id', 144)
	->go();
echo "<pre>";
	echo print_r($conn->info());
echo "</pre>";
echo $conn->lastInsertId();