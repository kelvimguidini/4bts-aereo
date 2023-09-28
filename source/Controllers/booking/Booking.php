<?php

namespace Source\Controllers\booking;

use Exception;
use Source\Controllers\Controller;
use Source\Model\Repository\BookingRepository;
use Source\Service\AereoService;
use Source\Traits\AppResponseTrait;
use YaLinqo\Enumerable;

class Booking extends Controller
{
    use AppResponseTrait;

    /**
     * @var \Source\Service\AereoService
     */
    private $aereoService;

    /**
     * @var \Source\Model\Repository\BookingRepository
     */
    private $bookingRepository;

    /**
     * Undocumented function
     *
     * @param [type] $router
     * @param AereoService $aereoService
     */
    public function __construct($router)
    {
        parent::__construct($router);
        $this->aereoService = new AereoService();
        $this->bookingRepository = new BookingRepository();
    }

    /**
     * Recebe disponibilidade de vôos e retorna para página de reserva
     *
     * @param [type] $data
     * @return mixed
     */
    public function handlePage($data): void
    {
        echo $this->view->render("pages/booking/booking", [
            "data" => json_encode($data)
        ]);
    }

    /**
     *  Valida os valores das passagens com os dados de disponibilidade
     *
     * @param [type] $data
     * @return void
     */
    public function toTariff(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $queryParams = [
            "packageGroup" => "GW-CERT",
            "preferences" => "preferences=language=Apt_BR,persistLog=true,showPlayer=true,currency=BRL"
        ];

        try {
            $rateTokenDisponibilidade = "";

            if (empty($data['fligth'])) {
                throw new Exception("Rate token disponibilidade não encontrado.");
            }

            $rateTokenDisponibilidade = $data['fligth'];

            $result = $this->aereoService->getTarifacao([], $queryParams, $rateTokenDisponibilidade);

            if ($result['status'] != 200) {
                throw new Exception("Tarifação inválida, por favor verifique os dados e tente novamente.");
            }

            echo $this->successResponse($result);
        } catch (Exception $e) {
            echo $this->errorResponse([], $e->getMessage());
        }
    }

    /**
     *  Obtém os dados dos passageiros e salva a reserva
     *
     * @param [type] $data
     * @return mixed
     */
    public function save(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        try {

            # Salva o booking na API
            $result = $this->aereoService->saveBooking([], $this->getBookingData($data["bookings"]));
            if ($result['status'] != 200) {
                throw new Exception("Houve um erro ao salvar a(s) reserva(s), por favor verifique os dados e tente novamente.");
            }

            # Salva os detalhes do voo no banco de dados
            $datePartida = new \DateTime($data["tariff"]["priceGroup"]["segments"][0]["departureDate"]);
            $horaVooPartida = $datePartida->format('H:i:s');
            $dateChegada = new \DateTime($data["tariff"]["priceGroup"]["segments"][count($data["tariff"]["priceGroup"]["segments"]) - 1]["arrivalDate"]);
            $horaVooChegada = $dateChegada->format('H:i:s');


            $data["Guid"] = $result["data"]["booking"]["airs"][0]["bookingToken"];
            $data["IdComposicao"] = "";
            $data["NumeroVoo"] = $data["tariff"]["priceGroup"]["segments"][0]["legs"][0]["flightNumber"];
            $data["IdCia"] = "";
            $data["De"] = $data["availability"]["origin"];
            $data["Para"] = $data["availability"]["destiny"];
            $data["DataVooPartida"] = $data["tariff"]["priceGroup"]["segments"][0]["departureDate"];
            $data["HoraVooPartida"] = $horaVooPartida;
            $data["DataHoraPartida"] =  $horaVooPartida;
            $data["UTCPartida"] = "0000-00-00 00:00:00";
            $data["DataVooChegada"] = $data["tariff"]["priceGroup"]["segments"][0]["arrivalDate"];
            $data["HoraVooChegada"] = $horaVooChegada;
            $data["DataHoraChegada"] = "0000-00-00 00:00:00";
            $data["DataVooPartidaRetorno"] = $horaVooChegada;
            $data["UTCChegada"] = "";
            $data["Duracao"] = (string) $data["tariff"]["priceGroup"]["segments"][0]["duration"];
            $data["Aeronave"] = "";
            switch ($data["availability"]["airType"]) {
                case "roundTrip":
                    $data["RoundOneTrip"] = "R";
                    break;
                case "oneWay":
                    $data["RoundOneTrip"] = "O";
                    break;
                case "mult":
                    $data["RoundOneTrip"] = "M";
                    break;
            }
            $data["localizador"] = $result["data"]["booking"]["airs"][0]["gds"]["reservationCode"];

            $idDetalhes = $this->bookingRepository->saveFlightsDetails($data);

            if ($idDetalhes == 0) {
                throw new Exception("Houve um erro ao salvar o(s) detalhes do voo, por favor verifique os dados e tente novamente.");
            }

            # Salva o preço do voo no banco de dados

            $fareADT = from($data["tariff"]["priceGroup"]["fareGroup"]["fares"])->where(function ($fares) {
                return $fares['passengersType'] == "ADT";
            })->select(function ($fares) {
                return $fares["priceWithTax"];
            })->toString();

            $fareCHD = from($data["tariff"]["priceGroup"]["fareGroup"]["fares"])->where(function ($fares) {
                return $fares['passengersType'] == "CHD";
            })->select(function ($fares) {
                return $fares["priceWithTax"];
            })->toString();

            $fareINF = from($data["tariff"]["priceGroup"]["fareGroup"]["fares"])->where(function ($fares) {
                return $fares['passengersType'] == "INF";
            })->select(function ($fares) {
                return $fares["priceWithTax"];
            })->toString();

            $data["TarifaFull"] = (float)str_replace(',', '.', $fareADT);
            $data["TarifaChd"] = (float)str_replace(',', '.', $fareCHD);
            $data["TarifaInf"] = (float)str_replace(',', '.', $fareINF);

            $data["IdVoo"] = $idDetalhes;
            $data["TarifaNet"] = (float)str_replace(',', '.', $data["tariff"]["priceGroup"]["fareGroup"]["priceWithoutTax"]);
            $data["TarifaVenda"] = (float)str_replace(',', '.', $result["data"]["booking"]["totalOrderPrice"]);

            $data["TarifaInfPacote"] = 0;
            $data["TarifaChdPacote"] = 0;
            $data["TarifaVendaPacote"] = 0;
            $data["TaxaDU"] = 0;
            $data["TaxaAeroporto"] = 0;
            $data["MoedaApresentacao"] = $data["tariff"]["priceGroup"]["fareGroup"]["currency"];
            $data["MoedaCambio"] = $data["tariff"]["priceGroup"]["fareGroup"]["currency"];
            $data["PeriodoInicial"] = $datePartida;
            $data["PeriodoFinal"] = $dateChegada;
            $data["RegraTarifaria"] = "";
            $data["BaseTarifaria"] = $data["tariff"]["priceGroup"]["segments"][0]["legs"][0]["fareBasis"];
            $idPrices = $this->bookingRepository->saveFlightsPrices($data);

            if ($idPrices == 0) {
                throw new Exception("Houve um erro ao salvar o preço do voo, por favor verifique os dados e tente novamente.");
            }

            foreach ($data["bookings"] as $item) {
                # Salva os dados do passageiro no banco de dados
                $data["Name"] = $item["name"];
                $data["Surname"] = $item["lastName"];
                $data["Age"] =  date("Y") - (int) substr($item["birthDate"], 0, 4);
                $data["Cpf"] = $item["documentType"] == "CPF" ? $item["document"] : "";
                $data["Title"] = "";
                $data["MainPax"] = 0;
                $data["isChild"] = $item["peopleType"] != "Adulto" ? 1 : 0;
                $data["Address"] = $item["country"] . "" . $item["street"] . "" . $item["complement"];
                $data["City"] = $item["city"];
                $data["ZipCode"] = $item["zipCode"];
                $data["State"] = $item["state"];
                $data["AddressNumber"] = $item["number"];
                $data["AddressComplement"] = "";
                $data["Email"] = $item["email"];
                $data["PhoneDDD"] = (int) substr($item["phone"], 1, 2);
                $data["PhoneDDI"] = "";
                $data["PhoneNumber"] = substr(str_replace("-", "", $item["phone"]), 5, 13);
                $data["IdVoo"] = $idDetalhes;
                $idPax = $this->bookingRepository->saveFlightsPax($data);

                if (!$idPax) {
                    throw new Exception("Houve um erro ao salvar o(s) passageiro(s), por favor verifique os dados e tente novamente.");
                }
            }

            # Salvar dados na sessão
            if (!isset($_SESSION["aereo"])) {
                $_SESSION["aereo"] = array();
            }

            $itemAereo = array();


            $itemAereo["idvoo"] = $idDetalhes;
            $itemAereo["Token"] = $result["data"]["booking"]["airs"][0]["bookingToken"];
            $itemAereo["qtd_adultos"] = $data["availability"]["adultos"];
            $itemAereo["qtd_criancas"] = $data["availability"]["criancas"];
            $itemAereo["nome"] = $data["availability"]["origin"] . " - " . $data["availability"]["destiny"] . " # " . $result["data"]["booking"]["airs"][0]["gds"]["reservationCode"];
            $itemAereo["data"] = new \DateTime();
            $itemAereo["valor"] = $data["tariff"]["priceGroup"]["fareGroup"]["priceWithTax"];
            $itemAereo["Taxas"] = $data["tariff"]["priceGroup"]["fareGroup"]["priceWithTax"] - $data["tariff"]["priceGroup"]["fareGroup"]["priceWithoutTax"];
            $itemAereo["dataLimiteParaEmissao"] = $result["data"]["booking"]["issueDateTimeLimit"];

            $_SESSION["aereo"][] = $itemAereo;

            echo $this->successResponse($itemAereo);
        } catch (Exception $e) {
            echo $this->errorResponse([], $e->getMessage());
        }
    }

    /**
     *  Valida os valores das passagens com os dados de disponibilidade
     *
     * @param [type] $data
     * @return void
     */
    public function toIssue($array): void
    {

        try {

            $errorMessage = null;


            /* busca a disponibilidade */
            $valido = true;


            if (!isset($array["idvoo"]) || $array["idvoo"] == "") {
                $valido = false;
                $errorMessage[] = array(
                    "campo" => "idvoo",
                    "mensagem" => "Campo obrigatório"
                );
            }

            if ($valido) {

                $detalhes = $this->bookingRepository->findFlightDetails($array["idvoo"]);
                $values = $this->bookingRepository->findFlightPrices($array["idvoo"]);

                $aereoService = new AereoService();
                $payload = $this->getIssueData($detalhes->Guid, $values->TarifaVenda);

                $result = $aereoService->saveBilhete([], $payload);

                foreach ($result["data"]["airTickets"] as $bilhete) {
                    $data = array();

                    $data["Token"] = $bilhete["airTokens"][0]["reservationToken"];
                    $data["IdVoo"] = $array["idvoo"];

                    $data["codigo"] = $bilhete["code"];
                    $data["localizador"] = $bilhete["gds"]["reservationCode"];
                    $data["Status"] = $bilhete["status"];

                    $data["QtdAdultos"] = $bilhete["pax"]['passengersType'] == "ADT" ? 1 : 0;
                    $data["QtdCriancas"] =  $bilhete["pax"]['passengersType'] == "CHD" || $bilhete["pax"]['passengersType'] == "INF" ? 1 : 0;

                    $data["Valor"] = $bilhete["payments"][0]["value"];
                    $data["Moeda"] = $bilhete["payments"][0]["currency"];

                    $data["tipo"] = 'ON';
                    $data["id_venda"] = 0;

                    $Object = new \DateTime();
                    $DateAndTime = $Object->format("Y-m-d h:i:s a");
                    $data["DataCadastro"] = $DateAndTime;

                    $this->bookingRepository->saveFlightsSales($data);
                }
            }
        } catch (Exception $e) {
            if ($result["airTickets"]["gds"]["reservationCode"]) {

                $errorMessage[] = array(
                    "campo" => "Localizador",
                    "mensagem" => "Reserva gerada no fornecedor com o localizador: " . $result["airTickets"]["gds"]["reservationCode"]
                );
            }

            $errorMessage[] = array(
                "mensagem" => $e->getMessage()
            );
            $valido = false;
        }

        header('Content-Type: application/json');
        echo json_encode([
            "success" => $valido,
            "result" => $result,
            "errors" => $errorMessage
        ]);
    }


    /**
     *  Valida os valores das passagens com os dados de disponibilidade
     *
     * @param [type] $data
     * @return void
     */
    public function list(): void
    {
        try {

            $errorMessage = null;
            $result = [];
            $voos = from($this->bookingRepository->getAllFlightsFilter(""))->orderByDescending(function ($voo) {
                return $voo->data->Id;
            });


            foreach ($voos as $voo) {
                $data = array();

                $values = $this->bookingRepository->findFlightPrices($voo->data->Id);

                $data["values"] = $values;
                $data["flight"] = $voo->data;

                $result[] = $data;
            }
        } catch (Exception $e) {

            $errorMessage[] = array(
                "" => $e->getMessage()
            );
            $valido = false;
        }

        $head = $this->seo->optimize(
            "Booking :: " . site("name"),
            "Reservas ativas",
            $this->router->route("booking.list"),
            routeImage("Aéreo"),
            false
        )->render();

        echo $this->view->render("pages/booking/list", [
            "head" => $head,
            "success" => $valido,
            "result" => $result,
            "errors" => $errorMessage
        ]);
    }


    public function details(): void
    {
        try {
            $valido = true;
            $errorMessage = null;

            $meuPost = file_get_contents("php://input");
            $array = (array) json_decode($meuPost);

            if (isset($array["idvoo"])) {
                $bd = $this->bookingRepository->findFlightDetails($array["idvoo"]);
            } else if (isset($array["localizador"])) {
                $bd = $this->bookingRepository->findFlightDetailsByLoc($array["localizador"]);
            }

            $bd = $this->bookingRepository->findFlightDetails($array["idvoo"]);
            $result = $this->aereoService->importBooking([], $bd->Guid);

            if ($result['status'] != 200) {
                throw new Exception("Houve um erro ao salvar a(s) reserva(s), por favor verifique os dados e tente novamente.");
            }
        } catch (Exception $e) {

            $errorMessage[] = array(
                "" => $e->getMessage()
            );
            $valido = false;
        }

        header('Content-Type: application/json');

        $resposta = json_encode([
            "success" => $valido,
            "result" => $result["data"]["airBookings"][0]["booking"],
            "errors" => $errorMessage
        ]);

        echo $resposta;
    }


    /**
     * Monta a estrutura de dados para salvar um booking a partir dos parâmetros do usuário
     * @param array $passenger
     * @return array
     */
    private function getBookingData(array $booking): array
    {
        $paxs = array();
        foreach ($booking as $passenger) {
            $paxs[] = [
                "address" => [
                    "city" => $passenger["city"],
                    "complement" => $passenger["complement"],
                    "county" => $passenger["country"],
                    "number" => $passenger["number"],
                    "state" => $passenger["state"],
                    "street" => $passenger["street"],
                    "zipCode" => $passenger["zipCode"],
                ],
                "birthDate" => [
                    (int) substr($passenger["birthDate"], 0, 4),
                    (int) substr($passenger["birthDate"], 5, -3),
                    (int) substr($passenger["birthDate"], -2, 9),
                ],
                "documents" => [
                    [
                        "doc" => $passenger["document"],
                        "type" => $passenger["documentType"]
                    ]
                ],
                "email" => $passenger["email"],
                "firstName" => $passenger["name"],
                "gender" => $passenger["gender"],
                "id" => 1,
                "lastName" => $passenger["lastName"],
                "phones" => [
                    [
                        "internationalCode" => 55,
                        "localCode" => (int) substr($passenger["phone"], 1, 2),
                        "number" => substr(str_replace("-", "", $passenger["phone"]), 5, 13),
                        "type" => "LANDLINE"
                    ]
                ]
            ];
        }
        return [
            "emitter" => EMITTER,
            "orderItems" => [
                "airBooking" => [
                    "tokenizedRateTokens" => $booking[0]["theTariffToken"]
                ]
            ],
            "paxs" => $paxs
        ];
    }



    public function getSeats($data): void
    {
        $errorMessage = [];
        $result = [];

        $valido = true;

        if ($data["bookingToken"] == null || $data["bookingToken"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "bookingToken",
                "mensagem" => "Valor não pode ser vazio"
            );
        }

        if ($data["legId"] == null || $data["legId"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "legId",
                "mensagem" => "Valor não pode ser vazio"
            );
        }

        if ($data["seatId"] == null || $data["seatId"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "seatId",
                "mensagem" => "Valor não pode ser vazio"
            );
        }


        if ($valido) {
            $aereoService = new AereoService();
            $arrayPax = [];

            try {
                $result = $aereoService->seats([], $data);
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


    public function cancelIssue($data): void
    {
        $errorMessage = [];
        $result = [];

        $valido = true;

        if ($data["bookingToken"] == null || $data["bookingToken"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "bookingToken",
                "mensagem" => "Valor não pode ser vazio"
            );
        }

        if ($valido) {
            $aereoService = new AereoService();
            $arrayPax = [];

            try {
                $result = $aereoService->deleteBilhete([], $data["bookingToken"]);
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

    public function cancel($data): void
    {
        $errorMessage = [];
        $result = [];

        $valido = true;

        if ($data["bookingToken"] == null || $data["bookingToken"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "bookingToken",
                "mensagem" => "Valor não pode ser vazio"
            );
        }


        if ($valido) {
            $aereoService = new AereoService();
            $arrayPax = [];

            try {
                $result = $aereoService->deleteBooking([], $data["bookingToken"]);
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

    public function setSeats($data): void
    {
        $errorMessage = [];
        $result = [];

        $valido = true;

        if ($data["bookingToken"] == null || $data["bookingToken"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "bookingToken",
                "mensagem" => "Valor não pode ser vazio"
            );
        }

        if ($data["legId"] == null || $data["legId"] == "") {
            $valido = false;
            $errorMessage[] = array(
                "campo" => "legId",
                "mensagem" => "Valor não pode ser vazio"
            );
        }

        if ($valido) {
            $aereoService = new AereoService();
            $arrayPax = [];

            try {
                $result = $aereoService->seats([], $data);
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


    /**
     * Monta a estrutura de dados para salvar um booking a partir dos parâmetros do usuário
     * @param array $passenger
     * @return array
     */
    private function getIssueData(string $bookingToken, string $bookingValue): array
    {
        return [
            "emitter" => EMITTER,
            "bookingToken" => $bookingToken,
            "issueToken" => null,
            "payment" => [
                "type" => "INVOICE",
                "value" => $bookingValue
            ],
        ];
    }
}
