### Instalação
```bash
composer danielfpedro/datasource
```

### Início
```php
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

$conn = new Datasource($config);
```

### Raw Query
```php
$conn
	->rawQuery('SELECT * FROM artigos')
	->go();
```

**Raw Query** usa `prepared statement`, caso você use algum `placeholder` informe no `::go()` os seus respectivos valores, exemplo:
```php
$conn
	->rawQuery('SELECT * FROM artigos WHERE id = :id')
	->go(['id' => 1]);
```


