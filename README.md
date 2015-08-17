# Datasource
Classe escrita sobre o `PDO` para agilizar algumas tarefas básicas.

## Instalação
```bash
composer danielfpedro/datasource
```

## Início
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

## Raw Query
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
### Insert
```html
<form mehotd="POST">
	<input type="text" name="titulo">
	<textarea name="texto"></textarea>
</form>
```
```php
$conn
	->insertInto('artigos')
	->values($_POST)
	->go();
````

## Evitando ataques

Uma prática muito comum é namear os campos de um formulário `HTML` com o mesmo nome dos campos do `Banco de Dados` e depois no insert informar apenas `$_POST` como valor.
Agora imagine que você tem uma table chamada `artigos` por padrão o campo `ativo` é `0`, ou seja, todos os artigos inseridos ficam inativos até que o revisor verifique para depois publicá-lo ou não.

Um usuáro mal itencionado pode facilmente injetar um campo de texto no formulário e nomeá-lo como `ativo`, logo os artigos que ele adicionar serão gravados com o valor de `1`, burlando assim o valor default.

Para evitar este ataque você pode informar quais campos você deseja que sejam salvos.
```html
<form mehotd="POST">
	<input type="text" name="titulo">
	<textarea name="texto"></textarea>
	<input type="text" name="ativo" value="1"><-- Injetado -->
</form>
```
```php
$conn
	->insertInto('artigos')
	->values($_POST, ['titulo', 'texto']) // Apenas título e texto serão salvos.
	->go();
```

## Update
```php
$conn
	->update('artigos')
	->set(['texto' => 'Boa noite.'])
	->where('id', 1)
	->go();
```
## Delete
```php
$conn
	->delete('artigos')
	->where('id', 1)
	->go();
```

**Obs.:** O `where` aceita apenas uma condição, caso necessite de condições mais elaboradas `rawQuery` deverá ser usado.

## Geters
* ::bindValues() - Valores que forão usados no `bind`
* ::query()
* ::info() - `Query` e valores do `Bind` do último `::go()`
* ::rowsAffected() - Linhas afetadas
* ::lastInsertId() - Último `ID` inserido

## Helper
```php
$data = ['titulo' => 'Olá', 'texto' => 'Bom dia.', 'dt_cadastro' => Datasource::now()];
```