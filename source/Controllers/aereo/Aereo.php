<?php

namespace Source\Controllers\aereo;

require __DIR__ . '../../../Model/Repository/AcessosRepository.php';

use Exception;
use Source\Controllers\Controller;
use Source\Service\AereoService;
use YaLinqo\Enumerable;


class Aereo extends Controller
{
    /**
     * Aereo constructor.
     * @param $router
     */
    public function __construct($router)
    {
        parent::__construct($router);
    }


    public function home(): void
    {
        $result = [];
        $head = $this->seo->optimize(
            "Aéreo :: " . site("name"),
            "Página de aéreo",
            $this->router->route("aereo.home"),
            routeImage("Aéreo"),
            false
        )->render();

        echo $this->view->render("pages/aereo/aereo", [
            "head" => $head,
        ]);
    }


    public function disponibilidade(): void
    {
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        $errorMessage = [];
        $result = [];

        $filtros = new \stdClass;

        /* busca a disponibilidade */
        $valido = true;

        $meuPost = file_get_contents("php://input");
        $array = (array) json_decode($meuPost);

        if ($array["adultos"] == 0 && $array["criancas"] == 0) {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "Passageiros",
                "mensagem" => "Valor deve ser maior do que 0"
            );
        }
        if ($array["adultos"] < 0) {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "Adultos",
                "mensagem" => "Valor não pode ser negativo"
            );
        }
        if ($array["criancas"] < 0) {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "Crianças",
                "mensagem" => "Valor não pode ser negativo"
            );
        }
        if (!isset($array["trechos"]) || count($array["trechos"]) == 0) {
            $valido = false;
            $errorMessage[] = array(
                "mensagem" => "Para onde vai viajar?"
            );
        }
        foreach ($array["trechos"] as $trecho) {
            if (!isset($trecho->embarque) || $trecho->embarque == "") {
                $valido = false;
                $errorMessage[] = array(
                    "campo" => "Data da Ida",
                    "mensagem" => "Campo obrigatório"
                );
            } else if (strtotime($trecho->embarque) < strtotime('today GMT')) {
                $valido = false;
                $errorMessage[] = array(
                    "campo" => "Datas",
                    "mensagem" => "Data de volta deve ser maior do que data de ida"
                );
            }
            if (!isset($trecho->origin) || $trecho->origin == "") {
                $valido = false;
                $errorMessage[] = array(
                    "campo" => "Origem",
                    "mensagem" => "Campo obrigatório"
                );
            }
            if (!isset($trecho->destiny) || $trecho->destiny == "") {
                $valido = false;
                $errorMessage[] = array(
                    "campo" => "Destino",
                    "mensagem" => "Campo obrigatório"
                );
            }

            if ($array["airType"] == 'roundTrip') {
                if (!isset($trecho->volta) || $trecho->volta == "") {
                    $valido = false;
                    $errorMessage[] = array(
                        "campo" => "Data da Volta",
                        "mensagem" => "Campo obrigatório"
                    );
                } else if (strtotime($trecho->volta) < strtotime($trecho->embarque)) {
                    $valido = false;
                    $errorMessage[] = array(
                        "campo" => "Datas",
                        "mensagem" => "Data de volta deve ser maior do que data de ida"
                    );
                }
            }
        }


        $voos = [];
        if ($valido) {
            $aereoService = new AereoService();
            $arrayPax = [];
            for ($x = 0; $x < $array["adultos"]; $x++) {
                $arrayPax[] = array('age' => 18);
            }
            for ($x = 1; $x <= $array["criancas"]; $x++) {
                $arrayPax[] = array('age' => $array["childsAge_" . $x]);
            }
            $routes = "";
            foreach ($array["trechos"] as $trecho) {
                if ($array["airType"] == 'roundTrip') {

                    $routes = $trecho->origin . "," . $trecho->destiny . "," . $trecho->embarque . "+" . $trecho->destiny . "," . $trecho->origin . "," . $trecho->volta;
                    break;
                } else {
                    if ($routes != "") {
                        $routes .= "+";
                    }
                    $routes .= $trecho->origin . "," . $trecho->destiny . "," . $trecho->embarque;
                }
            }

            $queryParams = [
                "pax" => $arrayPax,
                "routes" => $routes
            ];
            try {
                $result = $aereoService->getDisponibilidade([], $queryParams);

                $filterCias = [];
                $filterStops = [];
                foreach ($result['data']['flights'] as $fKey => $fligth) {
                    $voo = new \stdClass;
                    $seguimentos = [];

                    $valor = new \stdClass;
                    $valor->moeda = $fligth["fareGroup"]["currency"];
                    $valor->total = number_format($fligth["fareGroup"]["priceWithTax"], 2, ",", ".");
                    $valor->porTipoPax = [];

                    foreach ($fligth["fareGroup"]['fares'] as $pKey => $pax) {
                        $porTipoPax = new \stdClass;
                        switch ($pax["passengersType"]) {
                            case "ADT":
                                $porTipoPax->tipo = "Adulto";
                                break;
                            case "CHD":
                                $porTipoPax->tipo = "Criança";
                                break;
                            case "INF":
                                $porTipoPax->tipo = "Bebê";
                                break;
                        }

                        $porTipoPax->valor = number_format($pax['priceWithTax'], 2, ",", ".");

                        $valor->porTipoPax[] = $porTipoPax;
                    }

                    foreach ($fligth['segments'] as $sKey => $segment) {

                        $seguimento = new \stdClass;

                        //Partida
                        $aeroportoPartida = from($result['data']['meta']["airports"])->where(function ($airport) use ($segment) {
                            return $airport['iata'] == $segment['departure'];
                        })->select(function ($airport) {
                            return $airport['name'];
                        })->toString();

                        $seguimento->partida->airportShort = strrpos($aeroportoPartida, " ") === false ? $aeroportoPartida : strstr($aeroportoPartida, ' ', true) . '...';
                        $seguimento->partida->airportLong = $aeroportoPartida;
                        $seguimento->partida->iata = $segment['departure'];

                        $seguimento->partida->date = utf8_encode(strftime('%a, %d de %B', strtotime($segment['departureDate'])));
                        $seguimento->partida->time = utf8_encode(strftime('%H:%M', strtotime($segment['departureDate'])));

                        $seguimento->companyShort = is_numeric(explode(" ", $fligth['validatingBy']["name"])[0]) && isset(explode(" ", $fligth['validatingBy']["name"])[1]) ? explode(" ", $fligth['validatingBy']["name"])[1] : explode(" ", $fligth['validatingBy']["name"])[0];
                        $seguimento->companyLong = $fligth['validatingBy']["name"];

                        //geral
                        $seguimento->numeroConexao = $segment['numberOfStops'];

                        $data_inicio = new \DateTime($segment['departureDate']);
                        $data_fim = new \DateTime($segment['arrivalDate']);
                        $dateInterval = $data_inicio->diff($data_fim);
                        $seguimento->duracao = $dateInterval->h . 'h' . $dateInterval->i . 'min';

                        //Chegada
                        $aeroportoChegada = from($result['data']['meta']["airports"])->where(function ($airport) use ($segment) {
                            return $airport['iata'] == $segment['arrival'];
                        })->select(function ($airport) {
                            return $airport['name'];
                        })->toString();

                        $seguimento->chegada->airportShort = strrpos($aeroportoChegada, " ") === false ? $aeroportoChegada : strstr($aeroportoChegada, ' ', true) . '...';
                        $seguimento->chegada->airportLong = $aeroportoChegada;
                        $seguimento->chegada->iata = $segment['arrival'];

                        $seguimento->chegada->date = utf8_encode(strftime('%a, %d de %B', strtotime($segment['arrivalDate'])));
                        $seguimento->chegada->time = utf8_encode(strftime('%H:%M', strtotime($segment['arrivalDate'])));

                        //Bagagem
                        $seguimento->bagagemInclusa = $segment['fareProfile']['baggage']['isIncluded'];
                        $seguimento->bagagemQuantidade = $segment['fareProfile']['baggage']['quantity'];

                        $seguimento->rateToken = $segment['rateToken'];
                        $conexoes = [];
                        foreach ($segment['legs'] as $lKey => $leg) {
                            $conexao = new \stdClass;

                            //Partida
                            $aeroportoPartida = from($result['data']['meta']["airports"])->where(function ($airport) use ($leg) {
                                return $airport['iata'] == $leg['departure'];
                            })->select(function ($airport) {
                                return $airport['name'];
                            })->toString();

                            $conexao->partida->airportShort = strrpos($aeroportoPartida, " ") === false ? $aeroportoPartida :  strstr($aeroportoPartida, ' ', true) . '...';
                            $conexao->partida->airportLong = $aeroportoPartida;
                            $conexao->partida->iata = $leg['departure'];

                            $conexao->partida->date = utf8_encode(strftime('%a, %d de %B', strtotime($leg['departureDate'])));
                            $conexao->partida->time = utf8_encode(strftime('%H:%M', strtotime($leg['departureDate'])));


                            //geral
                            $data_inicio = new \DateTime($leg['departureDate']);
                            $data_fim = new \DateTime($leg['arrivalDate']);
                            $dateInterval = $data_inicio->diff($data_fim);
                            $conexao->duracao = $dateInterval->h . 'h' . $dateInterval->i . 'min';

                            $conexao->numeroVoo = $leg['flightNumber'];

                            //Chegada
                            $aeroportoChegada = from($result['data']['meta']["airports"])->where(function ($airport) use ($leg) {
                                return $airport['iata'] == $leg['arrival'];
                            })->select(function ($airport) {
                                return $airport['name'] . ', ' . $airport['location']['name'];
                            })->toString();

                            $conexao->chegada->airportShort = strrpos($aeroportoChegada, " ") === false ? $aeroportoChegada : strstr($aeroportoChegada, ' ', true) . '...';
                            $conexao->chegada->airportLong = $aeroportoChegada;
                            $conexao->chegada->iata = $leg['arrival'];

                            $conexao->partida->date = utf8_encode(strftime('%a, %d de %B', strtotime($leg['arrivalDate'])));
                            $conexao->chegada->time = utf8_encode(strftime('%H:%M', strtotime($leg['arrivalDate'])));

                            if ($lKey == 0) {
                                $seguimento->numeroVoo = $leg['flightNumber'];
                            }
                            $conexoes[] = $conexao;
                            $seguimento->conexoes =  $conexoes;
                        }

                        if ($array["airType"] == 'oneWay') {
                            $seguimento->trecho = "Ida";
                            $seguimento->trechoSelecionado = !from($seguimentos)->any(function ($seg) use ($seguimento) {
                                return $seguimento->trecho == $seg->trecho;
                            });
                        }
                        if ($array["airType"] == 'roundTrip') {
                            if ($seguimento->partida->date == utf8_encode(strftime('%a, %d de %B', strtotime($array["trechos"][0]->embarque)))) {
                                $seguimento->trecho = "Ida";
                            } else {
                                $seguimento->trecho = "Volta";
                            }
                            $seguimento->trechoSelecionado = !from($seguimentos)->any(function ($seg) use ($seguimento) {
                                return $seguimento->trecho == $seg->trecho;
                            });
                        }
                        if ($array["airType"] == 'mult') {
                            $i = 1;
                            foreach ($array["trechos"] as $trecho) {
                                if ($seguimento->partida->date == utf8_encode(strftime('%a, %d de %B', strtotime($trecho->embarque)))) {
                                    $seguimento->trecho = 'Trecho-' . $i;
                                    $seguimento->trechoSelecionado = !from($seguimentos)->any(function ($seg) use ($seguimento) {
                                        return $seguimento->trecho == $seg->trecho;
                                    });
                                }
                                $i++;
                            }
                        }

                        $seguimentos[] = $seguimento;

                        //Preenche Filtros
                        if (!from($filterCias)->any(function ($fil) use ($seguimento) {
                            return $fil == $seguimento->companyShort;
                        })) {
                            $filterCias[] = $seguimento->companyShort;
                        }

                        if (!from($filterStops)->any(function ($fil) use ($seguimento) {
                            return $fil == $seguimento->numeroConexao;
                        })) {
                            $filterStops[] = $seguimento->numeroConexao;
                        }
                    }
                    $voo->seguimentos =  $seguimentos;
                    $voo->valor =  $valor;
                    $voo->nome =  "voo_" . $fKey;

                    $voos[] = $voo;
                }

                $filtros->filterCias = $filterCias;
                $filtros->filterStops = $filterStops;
            } catch (Exception $e) {

                $errorMessage[] = array(
                    "mensagem" => $e->getMessage()
                );
                $valido = false;
            }
        }

        header('Content-Type: application/json');

        $resposta = json_encode([
            "success" => $valido,
            "result" => $voos,
            "filtros" => $filtros,
            "errors" => $errorMessage
        ]);

        echo $resposta;
    }


    public function regras($data): void
    {
        $errorMessage = [];
        $result = [];

        $valido = true;

        if ($data["rateTokenDisponibilidade"] == null || $data["rateTokenDisponibilidade"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "rateTokenDisponibilidade",
                "mensagem" => "Valor não pode ser vazio"
            );
        }
        if ($valido) {
            $aereoService = new AereoService();
            $arrayPax = [];

            try {
                $result = $aereoService->faresRules([], $data["rateTokenDisponibilidade"]);
            } catch (Exception $e) {

                $errorMessage[] = array(
                    "mensagem" => $e->getMessage()
                );
                $valido = false;
            }
        }

        header('Content-Type: application/json');

        $resposta = json_encode([
            "success" => $valido,
            "result" => $result,
            "errors" => $errorMessage
        ]);

        echo $resposta;
    }


    public function aeroportos($data): void
    {
        $saida = $data['direcao'] == 'Origem' ? CONFIGURACOES['aeroportosPermitidosOrigem'] : CONFIGURACOES['aeroportosPermitidosDestino'];

        if (count($saida) == 0) {
            $file = file_get_contents(__DIR__ . '/../../../views/pages/aereo/airports.json');
            $val = $data['pesquisa'];

            $json = json_decode($file);

            foreach ($json as $aeroporto) {
                if (strtoupper(substr($aeroporto->name_pt, 0, strlen($val))) == strtoupper($val) || strtoupper($aeroporto->iata) == strtoupper($val) || strtoupper(substr($aeroporto->city, 0, strlen($val))) == strtoupper($val)) {
                    array_push($saida, array("nome" => $aeroporto->name_pt, "nome_cidade" => $aeroporto->city, "iata" => $aeroporto->iata));
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($saida);
    }

    /**
     * @param $data
     */
    public function error($data): void
    {
        $error = filter_var($data['errcode'], FILTER_VALIDATE_INT);
        $head = $this->seo->optimize(
            "Ooops {$error}",
            "Página não encontrada",
            $this->router->route("aereo.error", ["errcode" => $error]),
            routeImage("{$error}"),
            false
        )->render();

        echo $this->view->render("pages/error/error", [
            "head" => $head,
            "error" => $error
        ]);
    }
}
