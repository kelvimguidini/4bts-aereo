<?php

namespace Source\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
use Source\Model\Repository\AcessosRepository;
use Source\Traits\AppResponseTrait;

class AereoService
{
    use AppResponseTrait;

    private $client;
    private $api_url;
    private $api_url_flights;

    public function __construct()
    {
        $this->api_url = "https://search-cvc-hom.reservafacil.tur.br/gwaereo/v0";
        $this->api_url_flights = "https://gwa-cvc-hom.reservafacil.tur.br/gwaereo/v0";
        $this->client = new Client();
    }


    /**
     * Obtém a disponibilidade de vôos para um determinado destino
     * @param array $headers
     * @param array $queryParams
     * @return array|string
     */
    public function getDisponibilidade(array $headers, array $queryParams)
    {
        $paramsHeaders = $this->handleHeaders($headers);
        $agesPax = "";
        foreach ($queryParams['pax'] as $pax) {
            $agesPax .= 'ages=' . $pax['age'] . '&';
        }

        $url = "$this->api_url/flights?" . $agesPax . "packageGroup=" . $paramsHeaders['packageGroup'] . "&routes=" . $queryParams['routes'];

        try {
            $response = $this->client->request("GET",  $url, [
                "headers" => $paramsHeaders
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }

    /**
     * Obtém o parcelamento de vôos
     *
     * @param array $headers
     * @param array $queryParams
     * @param string $rateTokenDisponibilidade
     * @return array|string
     */
    public function getParcelamento(array $headers, array $queryParams, string $rateTokenDisponibilidade)
    {
        $url = "$this->api_url/flights/installments/$rateTokenDisponibilidade";
        try {
            $response = $this->client->request("GET",  $url, [
                "headers" => $this->handleHeaders($headers),
                "query" => $this->handleQueryParams($queryParams)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }

    /**
     * Obtém a tarifacao
     *
     * @param array $headers
     * @param array $queryParams
     * @param string $rateTokenDisponibilidade
     * @return array|string
     */
    public function getTarifacao(array $headers, array $queryParams, string $rateTokenDisponibilidade)
    {
        $url = "$this->api_url_flights/flights/$rateTokenDisponibilidade?packageGroup=GW-CERT&preferences=preferences=language=Apt_BR,persistLog=true,showPlayer=true,currency=BRL";
        try {
            $response = $this->client->request("GET",  $url, [
                "headers" => $this->handleHeaders($headers),
                "query" => $this->handleQueryParams($queryParams)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }

    /**
     * Obtém as companhias aéreas
     *
     * @param array $headers
     * @return array|string
     */
    public function getAirLines($headers)
    {
        $url = "$this->api_url_flights/flights/airCompanies";
        try {
            $response = $this->client->request("GET",  $url, [
                "headers" => $this->handleHeaders($headers)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }

    /**
     * Salva bilhete
     *
     * @param array $headers
     * @param array $payload
     * @return array|string
     */
    public function saveBilhete(array $headers, array $payload)
    {
        $url = "$this->api_url/flights/issue";
        try {
            $response = $this->client->request("POST",  $url, [
                "headers" => $this->handleHeaders($headers),
                "body" => json_encode($payload)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }

    /**
     * Salva um booking
     *
     * @param array $headers
     * @param array $payload
     * @return array|string
     */
    public function saveBooking(array $headers, array $payload)
    {
        $url = "$this->api_url/flights/bookings";
        try {
            $response = $this->client->request("POST",  $url, [
                "headers" => $this->handleHeaders($headers),
                "body" => json_encode($payload)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }




    /**
     * Salva um booking
     *
     * @param array $headers
     * @param string $locationCode
     * @return array|string
     */
    public function importBooking(array $headers, string $bookingToken)
    {
        $url = "$this->api_url/flights/bookings/" . $bookingToken;
        try {
            $response = $this->client->request("GET",  $url, [
                "headers" => $this->handleHeaders($headers)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }


    /**
     * Consulta as regras da tarifa
     *
     * @param array $headers
     * @param string $locationCode
     * @return array|string
     */
    public function faresRules(array $headers, string $availabilityToken)
    {
        $url = "$this->api_url/flights/" . $availabilityToken . "/fareRules";
        try {
            $response = $this->client->request("GET",  $url, [
                "headers" => $this->handleHeaders($headers)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }



    /**
     * Consulta assentos disponíveis
     *
     * @param array $headers
     * @param string $locationCode
     * @return array|string
     */
    public function seats(array $headers, array $payload)
    {
        $url = "$this->api_url/flights/bookings/" . $payload["bookingToken"] . "/legs//" . $payload["legId"] . "/seats";

        try {
            $response = $this->client->request("GET",  $url, [
                "headers" => $this->handleHeaders($headers)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }


    /**
     * marcar assento
     *
     * @param array $headers
     * @param string $locationCode
     * @return array|string
     */
    public function setSeat(array $headers, array $payload)
    {
        $url = "$this->api_url/flights/bookings/" . $payload["bookingToken"] . "/legs//" . $payload["legId"] . "/seat/legs//" . $payload["seatId"];

        try {
            $response = $this->client->request("PUT",  $url, [
                "headers" => $this->handleHeaders($headers)
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }



    /**
     * Deletar um bilhete
     *
     * @param array $headers
     * @param array $payload
     * @param string $bookingToken
     * @return array|string
     */
    public function deleteBilhete(array $headers, string $bookingToken)
    {
        $url = "$this->api_url/flights/bookings/$bookingToken/tickets";
        try {
            $response = $this->client->request("DELETE",  $url, [
                "headers" => $this->handleHeaders($headers),
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }

    /**
     * Deletar um booking
     *
     * @param array $headers
     * @param array $payload
     * @param string $bookingToken
     * @return array|string
     */
    public function deleteBooking(array $headers, string $bookingToken)
    {
        $url = "$this->api_url/flights/bookings/$bookingToken";
        try {
            $response = $this->client->request("DELETE",  $url, [
                "headers" => $this->handleHeaders($headers),
            ]);
        } catch (RequestException $e) {
            throw new \Exception(Message::toString($e->getResponse()));
        }
        return [
            "status" => $response->getStatusCode(),
            "data" => json_decode($response->getBody()->getContents(), true)
        ];
    }

    private function handleHeaders(array $headers): array
    {
        if (count($headers) > 0) {
            return $headers;
        }

        $acessosRepo = new AcessosRepository();
        $headersTable = $acessosRepo->lerDadosAcesso();

        $headers = $headersTable != null ? $headersTable->data : [];
        return [
            "Content-Type" => "application/json",
            "Accept" => "application/json;charset=UTF-8",
            "Gtw-Branch-Id" => $headers->branch,
            "Gtw-Password" => $headers->senha,
            "Gtw-Username" => $headers->login,
            "Gtw-Group-Id" => $headers->GtwGroupId,
            "packageGroup" => $headers->packageGroup,
            "Gtw-Agency-Id" => $headers->GtwAgencyId,
            "Gtw-Agent-Sign" => $headers->GtwAgencyId,
            "Gwt-Transaction-Id" => $headers->GwtTransactionId
        ];
    }

    private function handleQueryParams(array $params): array
    {
        if (count($params) > 0) {
            return $params;
        }
        return [];
    }
}
