<?php

use Source\Service\AereoService;

it('Deve retornar a disponibilidade de vôos para um determinado destino', function () {
    $aereoService = new AereoService();
    $queryParams = [
        "ages" => "30",
        "packageGroup" => "GW-CERT",
        "routes" => "SAO,SSA,2022-04-10"
    ];
    $result = $aereoService->getDisponibilidade([], $queryParams);
    expect($result['status'])->toBe(200);
    // var_dump($result);
});

it('Deve retornar o parcelamento de vôos', function () {
    $aereoService = new AereoService();
    $queryParams = [
        "cardTypes" => "VI",
        "interest" => "false",
        "invoiceAmount" => "0"
    ];
    $rateTokenDisponibilidade = "PHJhdGVUb2tlbiBhZ3M9IkdXLUNFUlQiIGJyaT0iOTk5OSIgY21pPSJTR09MIiBjaWQ9Ijk0MSIgY3VyPSJCUkwiIGVjdD0iQlIiIGR0Zj0iMjAyMi0wNC0xMFQxNDowMDowMC4wMDBaIiBlc3Q9IkJBIiBlemk9IjEwNzYiIG1rcD0iMS4wMDAwIiBvZmQ9IntnOidTR09MX3I6MjInLGE6J1BSJyxiOidHUlVCRCcsYzonR1JVQkQnLGQ6J0dSVUJEJyxlOls0MjYzOV0sZjpbe2E6J0FEVCcsYjoxLGM6MTcyMS45MCxkOjM1LjUyLGU6MTcyLjE5LGg6MC4wMCxpOjAsajowLjAwLGs6MC4wMCxtOjAsZjpbXSxnOlszMF0sbDonU0dPTF8yMDIyLTAzLTI4VDIwOjI2OjIwLjM0Nid9XSxoOic5OTU3MDQ0NicsaTonOTk3NjljNWJiOTJjNDFjOScsbTo0MjYzOX0iIHB4cz0iMzAiIHBrZz0iR1ctQ0VSVCIgcGxjPSJCUkwiIGZ0cD0iQyIgcG90PSIxNzIxLjkwIiBwd3Q9IjE5MjkuNjEiIHByZD0iQUlSIiBzZHQ9IjIwMjItMDMtMjhUMjA6MjY6MjMuODY4WiIgc2drPSJ7Yzpbe2E6J0czMTQxMi0xMDA0MjIwOTM1LTAnLGI6J0dSVUJTQjEwMDQyMjExMjBQWScsYzonTycsZDonNzM4JyxlOidQTkdBQUcyRycsZjonTElHSFQnLGg6J0czJyxpOicnLGo6Jyd9LHthOidHMzE4MjYtMTAwNDIyMTIxMC0wJyxiOidCU0JTU0ExMDA0MjIxNDAwUFknLGM6J0knLGQ6JzczOCcsZTonUE5HQUFHMkcnLGY6J0xJR0hUJyxoOidHMycsaTonJyxqOicnfV0sZDonTFQnLGU6JzAuMCwwLjAnLGc6JzIwMzI0Myd9IiBzb3Q9IjE3MjEuOTAiIHN3dD0iMTkyOS42MSIgc2N0PSJCUiIgZHRpPSIyMDIyLTA0LTEwVDA5OjM1OjAwLjAwMFoiIHNzdD0iU1AiIHN6aT0iOTE0MSIgY29tPSIwLjAwMDAiIGNvcD0iMCIgaWN0PSIwLjAwMDAiIGljcD0iMCIgbWtpPSI0MjYzOSIgcGxhPSJHMyIgcHJmPSIxNzIuMTkiIG1lYz0iQ0lBIiBjcHA9IjAiIHJ0az0iTElHSFQiIGVtaT0iZmFsc2UiIHBweD0iMzAsMTcyMS45MCwxNzIxLjkwIiBjbXg9IjMwLDAuMDAsMC4wMCIgZG1raXM9IiIgcHJpcz0ie2JyZGlkOidMVCcsYnJkbm06J0xJR0hUJyxwcm1pZDonMjAzMjQzJ30iIHJmYj0idHJ1ZSIgcG1rPSIwLjAwIiBwbWY9IjEuMDAwMCIvPg==";
    $result = $aereoService->getParcelamento([], $queryParams, $rateTokenDisponibilidade);
    expect($result['status'])->toBe(200);
    // var_dump($result);
});

it('Deve retornar a tarifacao', function () {
    $aereoService = new AereoService();
    $queryParams = [
        "packageGroup" => "GW-CERT",
        "preferences" => "preferences=language=Apt_BR,persistLog=true,showPlayer=true,currency=BRL"
    ];
    $rateTokenDisponibilidade = "PHJhdGVUb2tlbiBhZ3M9IkdXLUNFUlQiIGJyaT0iOTk5OSIgY21pPSJMQVQiIGNpZD0iODk5IiBjdXI9IkJSTCIgZWN0PSJCUiIgZHRmPSIyMDIyLTA1LTEwVDA5OjAwOjAwLjAwMFoiIGVzdD0iQkEiIGV6aT0iMTA3NiIgbWtwPSIxLjAwMDAiIG9mZD0ie2c6J0xBVF9yOjUnLGE6J1BSJyxiOidTQU9LQicsYzonU0FPS0InLGQ6J1NBT0tCJyxlOls0MjYzOV0sZjpbe2E6J0FEVCcsYjoxLGM6MjAwMi45MCxkOjM2LjA2LGU6MjAwLjI5LGg6MC4wMCxpOjAsajowLjAwLGs6MC4wMCxtOjAsZjpbXSxnOlszMF0sbDonTEFUQU1fMjAyMi0wNC0wMVQxMTozOTo0MS44ODAnfV0saDonNTc1NjQ5MzEnLGk6J2EyN2NkNWZiNmJjZjRhYTYnLG06NDI2Mzl9IiBweHM9IjMwIiBwa2c9IkdXLUNFUlQiIHBsYz0iQlJMIiBmdHA9IkMiIHBvdD0iMjAwMi45MCIgcHd0PSIyMjM5LjI1IiBwcmQ9IkFJUiIgc2R0PSIyMDIyLTA0LTAxVDExOjM5OjQ1LjU4M1oiIHNnaz0ie2M6W3thOidMQTM2MjItMTAwNTIyMDY0MC0wJyxiOidDR0hTU0ExMDA1MjIwOTAwV1cnLGM6J08nLGQ6JzMyMCcsZTonTEpUWDBOQS9CMDAnLGY6J1BSRU1JVU0gRUNPTk9NWSBUT1AnLGg6J0xBJyxpOicnLGo6Jyd9XSxkOidSWScsZTonMycsZzonMjAyMzI1J30iIHNvdD0iMjAwMi45MCIgc3d0PSIyMjM5LjI1IiBzY3Q9IkJSIiBkdGk9IjIwMjItMDUtMTBUMDY6NDA6MDAuMDAwWiIgc3N0PSJTUCIgc3ppPSI5NjI2IiBjb209IjAuMDAwMCIgY29wPSIwIiBpY3Q9IjAuMDAwMCIgaWNwPSIwIiBta2k9IjQyNjM5IiBwbGE9IkxBIiBwcmY9IjIwMC4yOSIgbWVjPSJDSUEiIGNwcD0iMCIgcnRrPSJQUkVNSVVNIEVDT05PTVkgVE9QIiBlbWk9ImZhbHNlIiBwcHg9IjMwLDIwMDIuOTAsMjAwMi45MCIgY214PSIzMCwwLjAwLDAuMDAiIGRta2lzPSIiIHByaXM9InticmRpZDonUlknLGJyZG5tOidQUkVNSVVNX0VDT05PTVlfVE9QJyxwcm1pZDonMjAyMzI1J30iIHJmYj0idHJ1ZSIgcG1rPSIwLjAwIiBwbWY9IjEuMDAwMCIvPg==";
    $result = $aereoService->getTarifacao([], $queryParams, $rateTokenDisponibilidade);
    expect($result['status'])->toBe(200);
    // var_dump($result);
});

it('Deve retornar as companhias aéreas', function () {
    $aereoService = new AereoService();
    $result = $aereoService->getAirLines([]);
    expect($result['status'])->toBe(200);
    // var_dump($result);
});

it('Salva bilhete', function () {
    $aereoService = new AereoService();
    $payload = [
        "bookingToken" => "PGJvb2tpbmdUb2tlbiBicmk9Ijk5OTkiIGNpZD0iOTQxIiBjbWk9IlNHT0wiIHBsYT0iRzMiIGR0aT0iMjAyMi0wNC0xMFQwOTozNTowMC4wMDBaIiBkdGY9IjIwMjItMDQtMTBUMDk6MzU6MDAuMDAwWiIgbG9jPSJNSkpUTFEiIHByZD0iQUlSIiBnSWQ9IjEzMzM0OTUiIGNvbT0iMC4wMDAwIiBjb3A9IjAuMDAwMCIgaWN0PSIwLjAwMDAiIGljcD0iMC4wMDAwIiBta3A9IjEuMDAwMCIgbWtpPSI0MjYzOSIgb2lzPSIwIiBkbWtpcz0iIi8-",
        "issueToken" => null,
        "payment" => [
            "type" => "INVOICE",
            "value" => "1929.61"
        ],
        "emitter" => [
            "address" => [
                "city" => "São Paulo",
                "complement" => "teste 1",
                "county" => "BR",
                "number" => "111",
                "state" => "SP",
                "street" => "Rua do teste 1",
                "zipCode" => "11111-111"
            ],
            "email" => "",
            "firstName" => "RENATO",
            "lastName" => "RF",
            "phones" => [
                [
                    "internationalCode" => 55,
                    "localCode" => 11,
                    "number" => "4501-2711",
                    "type" => "LANDLINE"
                ]
            ]
        ]
    ];
    $result = $aereoService->saveBilhete([], $payload);
    expect($result['status'])->toBe(500);
    // var_dump($result);
});

it('Salva booking', function () {
    $aereoService = new AereoService();
    $payload = array(
        "emitter" => array(
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
                    "type" => "LANDLINE"
                )
            )
        ),
        "orderItems" => array(
            "airBooking" => array(
                "tokenizedRateTokens" => "PHJhdGVUb2tlbiBhZ3M9IkdXLUNFUlQiIGJyaT0iOTk5OSIgY21pPSJBWlVMIiBjaWQ9IjkyNCIgY3VyPSJCUkwiIGVjdD0iQlIiIGR0Zj0iMjAyMi0wNS0xMFQxNDoyMDowMC4wMDBaIiBlc3Q9IkJBIiBlemk9IjEwNzYiIG1rcD0iMC45MDkwOTEiIG9mZD0ie2c6J0FaVUxfcjoxMzEnLGE6J1BVJyxiOic1NzU2NDkzMScsYzonNTc1NjQ5MzEnLGQ6JzU3NTY0OTMxJyxlOls0MjY0MV0sZjpbe2E6J0FEVCcsYjoxLGM6MTIwOC45MCxkOjM2LjA2LGU6MC4wMCxoOjAuMDAsaToxMzIuOTgsajowLjAwLGs6MC4wMCxtOjEyMC44OSxmOltdLGc6WzMwXSxsOidBWlVMXzIwMjItMDQtMTRUMTc6MTU6NTAuMzcxJ31dLGg6JzU3NTY0OTMxJyxpOic3NmMxYmNlNzdmN2U0YWYzJyxtOjQyNjQxfSIgcHhzPSIzMCIgcGtnPSJHVy1DRVJUIiBwbGM9IkJSTCIgZnRwPSJNIiBwb3Q9IjEyMDguOTAiIHB3dD0iMTI0NC45NiIgcHJkPSJBSVIiIHNkdD0iMjAyMi0wNC0xNFQxODoyOToxNy44MDRaIiBzZ2s9InthOidBRH40MTMxfiB-fkNHSH4wNS8xMC8yMDIyIDA4OjE1fkNORn4wNS8xMC8yMDIyIDA5OjI1fn5eQUR-NDMwMH4gfn5DTkZ-MDUvMTAvMjAyMiAxMjo0MH5TU0F-MDUvMTAvMjAyMiAxNDoyMH5-JyxiOicwfkt-fkFEfksxMTRCR35CMTE0fn4wfjJ-flgnLGM6W3thOidBRDQxMzEtMTAwNTIyMDgxNS0wJyxiOidDR0hDTkYxMDA1MjIwOTI1S1knLGQ6JzMyMCcsZTonSzExNEJHJyxmOidGKycsaDonJyxpOidCMTE0JyxqOicnfSx7YTonQUQ0MzAwLTEwMDUyMjEyNDAtMCcsYjonQ05GU1NBMTAwNTIyMTQyMEtZJyxkOiczMjAnLGU6J0sxMTRCRycsZjonRisnLGg6JycsaTonQjExNCcsajonJ31dLGU6JzAuMCwwLjAnfSIgc290PSIxMzI5Ljc5IiBzd3Q9IjE0OTguODMiIHNjdD0iQlIiIGR0aT0iMjAyMi0wNS0xMFQwODoxNTowMC4wMDBaIiBzc3Q9IlNQIiBzemk9Ijk2MjYiIGNvbT0iMC4wMDAwIiBjb3A9IjAiIGljdD0iMC4wMDAwIiBpY3A9IjAiIG1raT0iNDI2NDEiIHBsYT0iQUQiIHByZj0iMC4wMCIgbWVjPSJPV04iIGNwcD0iMCIgcnRrPSJGKyIgZW1pPSJmYWxzZSIgcHB4PSIzMCwxMjA4LjkwLDEzMjkuNzkiIGNteD0iMzAsMC4wMCwwLjAwIiBkbWtpcz0iIiBwcmlzPSIiLz4="
            )
        ),
        "paxs" => array(
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
                "birthDate" => array(
                    1991,
                    1,
                    1
                ),
                "documents" => array(
                    array(
                        "doc" => "1111111111",
                        "type" => "RG"
                    )
                ),
                "email" => "joao@joao.com",
                "firstName" => "Samuel",
                "gender" => "M",
                "id" => 1,
                "lastName" => "Martins",
                "phones" => array(
                    array(
                        "internationalCode" => 55,
                        "localCode" => 11,
                        "number" => "1111-1111",
                        "type" => "LANDLINE"
                    )
                )
            )
        )
    );
    $result = $aereoService->saveBooking([], $payload);
    expect($result['status'])->toBe(200);
    // var_dump($result);
});

it('Deletar um booking', function () {
    $aereoService = new AereoService();
    $bookingToken = "PGJvb2tpbmdUb2tlbiBicmk9Ijk5OTkiIGNpZD0iOTQxIiBjbWk9IlNHT0wiIHBsYT0iRzMiIGR0aT0iMjAyMi0wNC0xMFQwOTozNTowMC4wMDBaIiBkdGY9IjIwMjItMDQtMTBUMDk6MzU6MDAuMDAwWiIgbG9jPSJERktJUlQiIHByZD0iQUlSIiBnSWQ9IjEzMzM0OTYiIGNvbT0iMC4wMDAwIiBjb3A9IjAuMDAwMCIgaWN0PSIwLjAwMDAiIGljcD0iMC4wMDAwIiBta3A9IjEuMDAwMCIgbWtpPSI0MjYzOSIgb2lzPSIwIiBkbWtpcz0iIi8-";
    $result = $aereoService->deleteBilhete([], $bookingToken);
    expect($result['status'])->toBe(200);
    // var_dump($result);
});

it('Deletar um bilhete', function () {
    $aereoService = new AereoService();
    $bookingToken = "PGJvb2tpbmdUb2tlbiBicmk9Ijk5OTkiIGNpZD0iOTQxIiBjbWk9IlNHT0wiIHBsYT0iRzMiIGR0aT0iMjAyMi0wNC0xMFQwOTozNTowMC4wMDBaIiBkdGY9IjIwMjItMDQtMTBUMDk6MzU6MDAuMDAwWiIgbG9jPSJERktJUlQiIHByZD0iQUlSIiBnSWQ9IjEzMzM0OTYiIGNvbT0iMC4wMDAwIiBjb3A9IjAuMDAwMCIgaWN0PSIwLjAwMDAiIGljcD0iMC4wMDAwIiBta3A9IjEuMDAwMCIgbWtpPSI0MjYzOSIgb2lzPSIwIiBkbWtpcz0iIi8-";
    $result = $aereoService->deleteBooking([], $bookingToken);
    expect($result['status'])->toBe(200);
    // var_dump($result);
});
