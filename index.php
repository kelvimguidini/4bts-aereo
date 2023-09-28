<?php
ob_start();
session_start();

header('Content-type: text/html; charset=utf-8');


setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'Portuguese_Brazil');
date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

$router = new Router(site());
$router->namespace("Source\Controllers\aereo");

/**
 * Painel - Login
 */
$router->group(null);
$router->get("/", "Aereo:home", "aereo.home");
$router->group(null);
$router->post("/", "Aereo:home", "aereo.home");

/**
 * API
 */
$router->group("api");
$router->get("/aeroportos/{pesquisa}/direcao/{direcao}", "Aereo:aeroportos", "aereo.aeroportos");
$router->get("/regras/{rateTokenDisponibilidade}", "Aereo:regras", "aereo.regras");
$router->post("/disponibilidade", "Aereo:disponibilidade", "aereo.disponibilidade");


/**
 * Booking
 */
$router->group("reserva")->namespace("Source\Controllers\booking");
$router->post("/", "Booking:handlePage", "reserva.home");
$router->post("/tarifar", "Booking:toTariff", "booking.toTariff");
$router->post("/salvar", "Booking:save", "booking.save");

$router->post("/emissao/{idvoo}", "Booking:toIssue", "booking.toIssue");

$router->get("/listar", "Booking:list", "booking.list");
$router->post("/detalhes", "Booking:details", "booking.details");

$router->get("/assentos/{{bookingToken}}/{{legId}}", "Booking:getSeats", "booking.getSeats");
$router->put("/assento/{{bookingToken}}/{{legId}}/{{seatId}}", "Booking:setSeats", "booking.setSeats");

$router->post("/cancelar/{{bookingToken}}", "Booking:cancel", "booking.cancel");
$router->post("/cancelarbilhete/{{bookingToken}}", "Booking:cancelIssue", "booking.cancelIssue");



/**
 * ERRORS ROUTERS
 */
$router->group("ops")->namespace("Source\Controllers\aereo");
$router->get("/{errcode}", "Aereo:error", "aereo.error");

/**
 * ROUTER PROCESS
 *
 */
$router->dispatch();

/**
 * ERRORS PROCESS
 */

if ($router->error()) {
    $router->redirect("aereo.error", ["errcode" => $router->error()]);
}

ob_end_flush();
