<?php

namespace Source\Model\Repository;

require __DIR__ . "../../../Model/Entity/FlightsDetails.php";

use Source\Model\Entity\FlightsDetails;
use Source\Model\Entity\FlightsPax;
use Source\Model\Entity\FlightsPrices;
use Source\Model\Entity\FlightsSales;
use stdClass;

class BookingRepository
{
    /**
     * Return an flights details
     *
     * @return mixed
     */
    public function getAllFlightsDetails(): mixed
    {
        $model = new FlightsDetails();
        $flightsDetails = $model->find()->fetch(true);
        return $flightsDetails;
    }

    /**
     * Return an flights price por idvoo
     *
     * @return mixed
     */
    public function findFlightPrices($idVoo): stdClass
    {
        $model = new FlightsPrices();
        $flightsDetails = $model->find("IdVoo=$idVoo")->fetch(true);
        if (count($flightsDetails) >= 1) {
            return $flightsDetails[0]->data;
        }

        return new \stdClass;
    }

    /**
     * Return an flights price por idvoo
     *
     * @return mixed
     */
    public function getAllFlightsFilter($filter): array
    {
        $model = new FlightsDetails();
        return $model->find($filter)->fetch(true);
    }

    /**
     * Return an flights por ID
     *
     * @return mixed
     */
    public function findFlightDetails($id): stdClass
    {
        $model = new FlightsDetails();
        $flightsDetails = $model->findById($id);
        return $flightsDetails->data;
    }


    /**
     * Return an flights por ID
     *
     * @return mixed
     */
    public function findFlightDetailsByLoc($localizador): stdClass
    {
        $model = new FlightsDetails();
        $flightsDetails = $model->find("localizador='$localizador'")->fetch(true);
        if (count($flightsDetails) >= 1) {
            return $flightsDetails[0]->data;
        }
        return  new \stdClass;
    }


    /**
     * Save flights details
     *
     * @param [type] $data
     * @return bool
     */
    public function saveFlightsDetails($data): int
    {
        $model = new FlightsDetails();

        $model->Guid = $data["Guid"];
        $model->IdComposicao = $data["IdComposicao"];
        $model->NumeroVoo =  $data["NumeroVoo"];
        $model->IdCia =  $data["IdCia"];
        $model->De =  $data["De"];
        $model->Para =  $data["Para"];
        $model->DataVooPartida = $data["DataVooPartida"];
        $model->HoraVooPartida = $data["HoraVooPartida"];
        $model->DataHoraPartida = $data["DataHoraPartida"];
        $model->UTCPartida = $data["UTCPartida"];
        $model->DataVooChegada = $data["DataVooChegada"];
        $model->HoraVooChegada = $data["HoraVooChegada"];
        $model->DataHoraChegada = $data["DataHoraChegada"];
        $model->DataVooPartidaRetorno = $data["DataVooPartidaRetorno"];
        $model->UTCChegada = $data["UTCChegada"];
        $model->Duracao = $data["Duracao"];
        $model->Aeronave = $data["Aeronave"];
        $model->localizador = $data["localizador"];

        $model->save();

        return $model->data->Id;
    }

    /**
     * Save flights prices
     *
     * @param [type] $data
     * @return bool
     */
    public function saveFlightsPrices($data): int
    {
        $model = new FlightsPrices();

        $model->IdVoo = $data["IdVoo"];
        $model->TarifaNet = str_replace(',', '.', $data["TarifaNet"]);
        $model->TarifaFull = str_replace(',', '.', $data["TarifaFull"]);
        $model->TarifaVenda = str_replace(',', '.', $data["TarifaVenda"]);
        $model->TarifaVendaPacote = str_replace(',', '.', $data["TarifaVendaPacote"]);
        $model->TarifaChd = str_replace(',', '.', $data["TarifaChd"]);
        $model->TarifaChdPacote = str_replace(',', '.', $data["TarifaChdPacote"]);
        $model->TarifaInf = str_replace(',', '.', $data["TarifaInf"]);
        $model->TarifaInfPacote = str_replace(',', '.', $data["TarifaInfPacote"]);
        $model->TaxaDU = str_replace(',', '.', $data["TaxaDU"]);
        $model->TaxaAeroporto = str_replace(',', '.', $data["TaxaAeroporto"]);
        $model->MoedaApresentacao = $data["MoedaApresentacao"];
        $model->MoedaCambio = $data["MoedaCambio"];
        $model->PeriodoInicial = $data["PeriodoInicial"];
        $model->PeriodoFinal = $data["PeriodoFinal"];
        $model->RegraTarifaria = $data["RegraTarifaria"];
        $model->BaseTarifaria = $data["BaseTarifaria"];

        $model->save();

        return $model->data->Id;
    }

    /**
     * Save flights pax
     *
     * @param [type] $data
     * @return bool
     */
    public function saveFlightsPax($data): int
    {
        $model = new FlightsPax();

        $model->Name = $data["Name"];
        $model->Surname = $data["Surname"];
        $model->Age = $data["Age"];
        $model->Cpf = $data["Cpf"];
        $model->Title = $data["Title"];
        $model->MainPax = $data["MainPax"];
        $model->isChild = $data["isChild"];
        $model->Address = $data["Address"];
        $model->City = $data["City"];
        $model->ZipCode = $data["ZipCode"];
        $model->State = $data["State"];
        $model->AddressNumber = $data["AddressNumber"];
        $model->AddressComplement = $data["AddressComplement"];
        $model->Email = $data["Email"];
        $model->PhoneDDD = $data["PhoneDDD"];
        $model->PhoneDDI = $data["PhoneDDI"];
        $model->PhoneNumber = $data["PhoneNumber"];
        $model->IdVoo = $data["IdVoo"];

        $model->save();

        return $model->data->id;
    }



    /**
     * Save flights sales
     *
     * @param [type] $data
     * @return bool
     */
    public function saveFlightsSales($data): bool
    {
        $model = new FlightsSales();

        $model->IdVoo = $data["IdVoo"];
        $model->QtdAdultos = $data["QtdAdultos"];
        $model->QtdCriancas = $data["QtdCriancas"];
        $model->Valor = $data["Valor"];
        $model->Moeda = $data["Moeda"];
        $model->Token = $data["Token"];
        $model->Status = $data["Status"];
        $model->tipo = $data["tipo"];
        $model->id_venda = $data["id_venda"];
        $model->DataCadastro = $data["DataCadastro"];


        $model->codigo = $data["codigo"];
        $model->localizador = $data["localizador"];

        $model->save();

        return $model->data->Id;
    }
}
