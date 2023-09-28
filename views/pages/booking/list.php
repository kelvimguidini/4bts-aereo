<?php $v->layout("theme/_theme"); ?>
<?php $v->start("styles"); ?>
<link rel="stylesheet" href="<?= asset("aereo/css/air.css"); ?>">
<link rel="stylesheet" href="<?= asset("aereo/css/main.css"); ?>">
<link rel="stylesheet" href="<?= asset("booking/css/form.css"); ?>">
<?php $v->end(); ?>

<div id="app" class="container body">

    <table class="table">
        <thead>
            <tr>
                <th>Localizador</th>
                <th>Origem</th>
                <th>Destino</th>
                <th>Data Partida</th>
                <th>Hora Partida</th>
                <th>Data Chegada</th>
                <th>Hora Chegada</th>
                <th>Tipo</th>
                <th>Bagagem</th>
                <th>Status</th>
                <th>Valor Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $voo) { ?>
                <tr>
                    <td><?= $voo['flight']->localizador ?></td>
                    <td><?= $voo['flight']->De ?></td>
                    <td><?= $voo['flight']->Para ?></td>
                    <td><?= utf8_encode(strftime('%d/%m/%Y', strtotime($voo['flight']->DataVooPartida)));  ?></td>
                    <td><?= utf8_encode(strftime('%H:%M', strtotime($voo['flight']->HoraVooPartida)));  ?></td>
                    <td><?= utf8_encode(strftime('%d/%m/%Y', strtotime($voo['flight']->DataVooChegada)));  ?></td>
                    <td><?= utf8_encode(strftime('%H:%M', strtotime($voo['flight']->HoraVooChegada)));  ?></td>
                    <td><?php
                        switch ($voo['flight']->RoundOneTrip) {
                            case "R":
                                echo "Ida e volta";
                                break;
                            case "O":
                                echo "Só ida";
                                break;
                            case "M":
                                echo "Multitrecho";
                                break;
                        } ?></td>
                    <td><?= $voo['flight']->Bagagem == "S" ? "Incluído" : "Sem Bagagem" ?></td>
                    <td><?= $voo['flight']->Status == "S" ? "Reservada" : "Cancelada" ?></td>
                    <td><?= $voo['values']->MoedaApresentacao ?> <?= number_format($voo['values']->TarifaVenda, 2, ",", "."); ?></td>
                    <td><button v-on:click="openFlight($event, <?= $voo['flight']->Id ?>)">Detalhes</button></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div id="abrirModalDetalhes" v-if="modalDetails == true" class="modal">
        <div class="modal-content">
            <span class="close" v-on:click="modalDetails = false">&times;</span>
            <h2>Detalhes da reserva {{detalhesVoo.airs[0].gds.reservationCode}}</h2>


            <div>
                <h3>Voos</h3>
                <table class="table" v-for="segment in detalhesVoo.airs[0].priceGroup.segments">
                    <thead>
                        <tr>
                            <th>Compahia</th>
                            <th>De</th>
                            <th>Para</th>
                            <th>Partida</th>
                            <th>Chegada</th>
                            <th>Número Voo</th>
                            <th>Status</th>
                            <th>Classe</th>
                            <th>Familia</th>
                            <th>Assento</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="leg in segment.legs">
                            <td>{{leg.operatedBy.name}}</td>
                            <td>{{leg.departure}}</td>
                            <td>{{leg.arrival}}</td>
                            <td>{{formatDateTimeBrazil(leg.departureDate)}}</td>
                            <td>{{formatDateTimeBrazil(leg.arrivalDate)}}</td>
                            <td>{{leg.flightNumber}}</td>
                            <td>{{leg.status}}</td>
                            <td>{{leg.seatClass.description}}</td>
                            <td>{{segment.fareProfile.fareFamily}}</td>
                            <td>{{leg.seatClass.code}}</td>
                            <td><button v-on:click="marcarAssento($event, airTicket.code)">Marcar Assento</button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr v-for="service in segment.fareProfile.services">
                            <td colspan="3"></td>
                            <td v-bind:class="{'text-blue': service.isIncluded == true, 'text-red': service.isIncluded == false}">{{service.type}}</td>
                            <td v-bind:class="{'text-blue': service.isIncluded == true, 'text-red': service.isIncluded == false}" colspan="6">{{service.description}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div>
                <h3>Passageiros</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Sobre nome</th>
                            <th>Genero</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pax in detalhesVoo.paxs">
                            <td>{{pax.firstName}}</td>
                            <td>{{pax.lastName}}</td>
                            <td>{{pax.gender == "M" ? "Masculino" : "Feminino"}}</td>
                            <td>{{pax.passengerType}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div>
                <h3>Valores</h3>
                <table class="table" v-for="air in detalhesVoo.airs">
                    <thead>
                        <tr>
                            <th>Tipo de Pax</th>
                            <th>Quantidade</th>
                            <th>Taxa</th>
                            <th>Tarifa</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="fare in air.priceGroup.fareGroup.fares">
                            <td>{{fare.passengersType}}</td>
                            <td>{{fare.passengersCount}}</td>
                            <th>{{air.priceGroup.fareGroup.currency}} {{formatMoney(fare.priceWithTax - fare.priceWithoutTax)}}</th>
                            <th>{{air.priceGroup.fareGroup.currency}} {{formatMoney(fare.priceWithoutTax)}}</th>
                            <th>{{air.priceGroup.fareGroup.currency}} {{formatMoney(fare.priceWithTax)}}</th>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2"></th>
                            <th>{{air.priceGroup.fareGroup.currency}} {{formatMoney(air.priceGroup.fareGroup.priceWithTax - air.priceGroup.fareGroup.priceWithoutTax)}}</th>
                            <th>{{air.priceGroup.fareGroup.currency}} {{formatMoney(air.priceGroup.fareGroup.priceWithoutTax)}}</th>
                            <th>{{air.priceGroup.fareGroup.currency}} {{formatMoney(air.priceGroup.fareGroup.priceWithTax)}}</th>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <div>
                <h3>Bilhetes</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Status</th>
                            <th>Data emissão</th>
                            <th>Passageiro</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="airTicket in detalhesVoo.airs[0].airTickets">
                            <td>{{airTicket.code}}</td>
                            <td>{{airTicket.status}}</td>
                            <td>{{formatDateTimeBrazil(airTicket.creationDate)}}</td>
                            <td>{{airTicket.pax.lastName}}, {{airTicket.pax.firstName}} - {{airTicket.pax.passengerType}}</td>
                            <td>

                                <button v-on:click="cancelar($event, airTicket.code, airTicket.Id)">Cancelar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div><button v-on:click="cancelarReserva($event)">Cancelar Reserva</button></div>
        </div>
    </div>


    <div id="abrirModal" v-if="modalValidation == true" class="modal">
        <div class="modal-content">
            <span class="close" v-on:click="modalValidation = false">&times;</span>
            <h2>Verifique esse(s) erro(s)</h2>
            <ul>
                <li v-for="(error, index) in errors">
                    <b v-if="error.campo">{{ error.campo }}:</b> <span>{{error.mensagem}}</span>
                </li>
            </ul>
        </div>
    </div>



</div>

<?php $v->start("scripts"); ?>
<script src="<?= asset("booking/js/details.js"); ?>"></script>
<?php $v->end(); ?>