<?php

/**
 * SITE CONFIG
 */
define("SITE", [
    "name" => "Eventos",
    "desc" => "Eventos",
    "domain" => "http://localhost/4bts-aereo",
    "locale" => "pt_BR",
    "root" => "http://localhost/4bts-aereo"
]);

/*
*
*/
define("CONFIGURACOES", [
    "ambiente" => "RFH", // HOMOLOGAÇÃO RESERVA FÁCIL
    "aeroportosPermitidosOrigem" => [],
    "aeroportosPermitidosDestino" => []
]);

/**
 * DATABASE CONNECT
 */
define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "mysql.dev.eventos.com.br",
    "port" => "3306",
    "dbname" => "deveventos",
    "username" => "deveventos",
    "passwd" => "8c5as78c3s5d",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);


/**
 * DATAS EMITTER
 */
define(
    "EMITTER",
    array(
        "address" => array(
            "city" => "São Paulo",
            "complement" => "teste 1",
            "county" => "BR",
            "number" => "111",
            "state" => "SP",
            "street" => "Rua do teste 1",
            "zipCode" => "11111-111"
        ),
        "email" => "",
        "firstName" => "RENATO",
        "lastName" => "RF",
        "phones" => array(
            array(
                "internationalCode" => 55,
                "localCode" => 11,
                "number" => "4501-2711",
                "type" => "LANDLINE",
            )
        )

    )
);

/**
 * Monta e retorna uma determinada rota da aplicação de acordo com o $path
 * @param string|null $uri
 * @return string
 */
function route(string $path = null): string
{
    if ($path) {
        return SITE['domain'] . "/{$path}";
    }
    return SITE['domain'];
}
