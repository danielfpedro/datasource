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
// Exemplo 1
$conn
	->rawQuery('SELECT * FROM artigos WHERE id = :id')
	->go(['id' => 1]);

```
### Insert
```html
<form mehotd="POST">
	<input type="text" name="titulo">
	<textarea name="texto"></textarea>
</form>
```php
$conn
	->insertInto('artigos')
	->values($_POST)
	->go();
````
```
## Evitando ataques

Imagine que no seu Banco por padrão o campo `ativo` é `0`, ou seja, todos os artigos inseridos ficam inativos até que o revisor verifique para publica-lo ou não.

Um usuáro mal itencionada pode facilmente injetar um campo de texto no formulário e nomeá-lo como `ativo` e os artigo que ele adiciona-se seria gravado como ativo, burlando o valor default.

```php
// $_POST = ['titulo' => 'Olá', 'texto' => 'Bom dia...', 'ativo' => 1]<- valor injetado
$conn
	->insertInto('artigos')
	->values($_POST)
	->go();
```

Para evitar este ataque você pode informar quais campos você deseja que sejam salvos.
```php
// $_POST = ['titulo' => 'Olá', 'texto' => 'Bom dia...', 'ativo' => 1]<- valor injetado
$conn
	->insertInto('artigos')
	->values($_POST, ['titulo', 'texto']) // Apenas titulo e texto serão salvos.
	->go();
```