<?php

use Source\Model\Repository\BookingRepository;

it('Should return all flights details', function () {
    $flightsDetails = new BookingRepository();

    foreach ($flightsDetails->getAllFlightsDetails() as $item) {
        expect($item)->toBeObject();
    }
});

it('Must save a new flights detail', function () {
    $flightsDetails = new BookingRepository();

    $data["Guid"] = "";
    $data["IdComposicao"] = "";
    $data["NumeroVoo"] = "";
    $data["IdCia"] = "";
    $data["De"] = "";
    $data["Para"] = "";
    $data["DataVooPartida"] = "0000-00-00 00:00:00";
    $data["HoraVooPartida"] = "0000-00-00 00:00:00";
    $data["DataHoraPartida"] = "0000-00-00 00:00:00";
    $data["UTCPartida"] = "0000-00-00 00:00:00";
    $data["DataVooChegada"] = "0000-00-00 00:00:00";
    $data["HoraVooChegada"] = "0000-00-00 00:00:00";
    $data["DataHoraChegada"] = "0000-00-00 00:00:00";
    $data["DataVooPartidaRetorno"] = "0000-00-00 00:00:00";
    $data["UTCChegada"] = "";
    $data["Duracao"] = "";
    $data["Aeronave"] = "";
    $data["RoundOneTrip"] = "";

    $result = $flightsDetails->saveFlightsDetails($data);
    expect($result)->toBeTrue();
    // var_dump("Result: ", $result);
});

it('Must save a new flights prices', function () {
    $flightsPrice = new BookingRepository();

    $data["IdVoo"] = 0;
    $data["TarifaNet"] = 0;
    $data["TarifaFull"] = 0;
    $data["TarifaVenda"] = 0;
    $data["TarifaVendaPacote"] = 0;
    $data["TarifaChd"] = 0;
    $data["TarifaChdPacote"] = 0;
    $data["TarifaInf"] = 0;
    $data["TarifaInfPacote"] = 0;
    $data["TaxaDU"] = 0;
    $data["TaxaAeroporto"] = 0;
    $data["MoedaApresentacao"] = "";
    $data["MoedaCambio"] = "";
    $data["PeriodoInicial"] = "0000-00-00 00:00:00";
    $data["PeriodoFinal"] = "0000-00-00 00:00:00";
    $data["RegraTarifaria"] = "";
    $data["BaseTarifaria"] = "";

    $result = $flightsPrice->saveFlightsPrices($data);
    expect($result)->toBeTrue();
    // var_dump("Result: ", $result);
});

it('Must save a new flights pax', function () {
    $flightsPax = new BookingRepository();

    $data["Name"] = "";
    $data["Surname"] = "";
    $data["Age"] = 0;
    $data["Cpf"] = "";
    $data["Title"] = "";
    $data["MainPax"] = 0;
    $data["isChild"] = 0;
    $data["Address"] = "";
    $data["City"] = "";
    $data["ZipCode"] = "";
    $data["State"] = "";
    $data["AddressNumber"] = 0;
    $data["AddressComplement"] = "";
    $data["Email"] = "";
    $data["PhoneDDD"] = "";
    $data["PhoneDDI"] = "";
    $data["PhoneNumber"] = "";
    $data["IdSales"] = 0;

    $result = $flightsPax->saveFlightsPax($data);
    expect($result)->toBeTrue();
    // var_dump("Result: ", $result);
});
