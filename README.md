# Base

Base necessária para começar a trabalhar

## Instalação

```sh
composer install
```

## Configurações básicas

Edite o arquivo `source/Config.php` e adicione uma conexão com o banco de dados e troque os paths relativos.

```php
<?php
/**
 * SITE CONFIG
 */
define("SITE", [
    "name" => "Eventos",
    "desc" => "Eventos",
    "domain" => "http://localhost/4bts/aereo",
    "locale" => "pt_BR",
    "root" => "http://localhost/4bts/aereo"
]);

/**
 * DATABASE CONNECT
 */
define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "aereo",
    "username" => "root",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

?>
```

## Tests

```
cd project_folder
```

```
./vendor/bin/pest
```
