<?php $v->layout("theme/_theme"); ?>
<?php $v->start("styles"); ?>
<link rel="stylesheet" href="<?= asset("aereo/css/air.css"); ?>">
<?php $v->end(); ?>


<div class="container main-header">
    <div class="background"><img src="https://cdn.pixabay.com/photo/2016/07/22/03/27/rio-de-janeiro-1534089_960_720.jpg" alt=""></div>
    <div class="row cover">
        <img src="https://cdn.pixabay.com/photo/2016/07/22/03/27/rio-de-janeiro-1534089_960_720.jpg" alt="">
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

<div class="container body">
    <form class="row" method="post" action="<?= route('reserva/'); ?>" enctype="multipart/form-data" autocomplete="off">

        <div class="radios">
            <label><span><input type="radio" v-model="airType" v-on:change="manterTrecho($event, true, true)" name="airType" value="oneWay" /> <strong></strong></span>Só Ida</label>
            <label><span><input type="radio" v-model="airType" v-on:change="manterTrecho($event, true, true)" name="airType" value="roundTrip" /> <strong></strong></span>Ida e Volta</label>
            <label><span><input type="radio" v-model="airType" name="airType" value="mult" /> <strong></strong></span>Multi destino</label>
        </div>
        <div class="content">
            <div class="area">
                <div class="oneWay">
                    <div class="options" v-for="(trecho, n) in trechos">
                        <div class="origin-destiny">
                            <svg v-if="trecho.originDestiny == 'origin'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                <path d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
                            </svg>
                            <input type="text" name="origin" v-show="trecho.originDestiny == 'origin'" ref="IOri" placeholder="Digite seu ponto de partida" />
                            <button v-on:click="trecho.originDestiny = 'origin'" type="button">
                                <span>
                                    <strong>Origem</strong>
                                    <span v-if="trecho.airportOrigem==''">Local de saída</span>
                                    <span v-if="trecho.airportOrigem!=''">{{trecho.airportOrigem}}</span>
                                </span>
                            </button>
                            <span v-if="trecho.originDestiny == 'origin'" v-on:click="trecho.originDestiny = false">x</span>
                        </div>
                        <div class="origin-destiny">
                            <svg v-if="trecho.originDestiny == 'destiny'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                <path d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z" />
                            </svg>
                            <input type="text" name="destiny" v-show="trecho.originDestiny == 'destiny'" ref="iDes" placeholder="Digite seu ponto de chegada" />
                            <button v-on:click="trecho.originDestiny = 'destiny'" type="button">
                                <span>
                                    <strong>Destino</strong>
                                    <span v-if="trecho.airportDestino==''">Local de chegada</span>
                                    <span v-if="trecho.airportDestino!=''">{{trecho.airportDestino}}</span>
                                </span>
                            </button>
                            <span v-if="trecho.originDestiny == 'destiny'" v-on:click="trecho.originDestiny = false">x</span>
                        </div>

                        <div class="boarding" v-bind:class="{'boarding-roundtrip': airType == 'roundTrip'}">
                            <input type="date" v-model="trecho.embarque" v-if="trecho.boarding" />
                            <button type="button" v-if="!trecho.boarding" v-on:click="trecho.boarding = true">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span v-if="trecho.embarque ==''"> Ida</span>
                                <span v-if="trecho.embarque !=''"> {{formatDateBrazil(trecho.embarque)}}</span>
                            </button>
                            <span v-if="trecho.boarding" v-on:click="trecho.boarding = false" class="close">x</span>
                        </div>

                        <div class="boarding boarding-volta" v-if="airType == 'roundTrip'">

                            <input type="date" v-model="trecho.volta" v-if="trecho.boardingVolta" />
                            <button v-if="!trecho.boardingVolta" type="button" v-on:click="trecho.boardingVolta = true">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span v-if="trecho.volta==''"> Volta</span>
                                <span v-if="trecho.volta!=''"> {{formatDateBrazil(trecho.volta)}}</span>
                            </button>
                            <span v-if="trecho.boardingVolta && airType == 'roundTrip'" v-on:click="trecho.boardingVolta = false" class="close">x</span>
                        </div>
                    </div>

                    <ul id="suggestions"></ul>
                    <div class="options">

                        <div v-if="airType == 'mult' && trechos.length < 8" class="search trecho">
                            <button v-on:click="manterTrecho($event)"><i class="fa-solid fa-plus"></i>Trecho</button>
                        </div>
                        <div v-if="airType == 'mult' && trechos.length > 1" class="search trecho">
                            <button v-on:click="manterTrecho($event, true)"><i class="fa-solid fa-trash"></i>Trecho</button>
                        </div>

                        <div v-bind:class="{'passengers-mult': airType == 'mult'}" class="passengers">
                            <button type="button" v-on:click="passengers = !passengers"><i class="fa-solid fa-user"></i>
                                {{numberAdults + numberChildren}} passageiro
                            </button>
                            <div class="quantity" :class="{open: passengers}">
                                <input type="hidden" name="adultos" v-model="numberAdults" />
                                <input type="hidden" name="criancas" v-model="numberChildren" />
                                <div class="btns">
                                    <div><span>Adultos</span><strong>por quarto</strong></div>
                                    <div>
                                        <button type="button" v-on:click="numberAdults-=1">-</button>
                                        <span>{{numberAdults}}</span>
                                        <button type="button" v-on:click="numberAdults+=1">+</button>
                                    </div>
                                </div>
                                <div class="btns">
                                    <div><span>Crianças</span><strong>por quarto</strong></div>
                                    <div>
                                        <button type="button" v-on:click="numberChildren-=1">-</button>
                                        <span>{{numberChildren}}</span>
                                        <button type="button" v-on:click="numberChildren+=1">+</button>
                                    </div>
                                </div>

                                <div class="children-months" v-for="index in numberChildren" :key="index">
                                    <h3>Qual a idade da {{index}}° criança</h3>
                                    <select v-model="childAge[index]">
                                        <option v-for="n in 11" :value="n">{{n}} {{n < 2 ? 'Ano' : 'Anos'}}</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="search">
                            <button v-on:click="callAvailability($event)"><i class="fa-solid fa-search"></i></button>
                        </div>

                    </div>
                </div>

            </div>

            <div class="flights" v-if="fligths.length > 0">

                <div class="flight" v-for="(fligth, fIndex) in fligths" v-bind:class="{'roundtrip': airType == 'roundTrip'}">

                    <div class="trecho">
                        <div v-for="(segment, sIndex) in fligth.seguimentos" v-bind:class="{'firsttrecho':segment.trechoSelecionado && sIndex >0 }">

                            <div class="going">
                                <input type="radio" :name="fligth.nome + '_' + segment.trecho" :checked="segment.trechoSelecionado" :value="sIndex" />
                                <div>
                                    <strong>{{segment.trecho}}</strong>
                                    <span>{{segment.numeroVoo}} -
                                        <span class="tooltip">
                                            {{segment.companyShort}}
                                            <span class="tooltiptext">
                                                {{segment.companyLong}}
                                            </span>
                                        </span>
                                    </span>
                                    <div>
                                        {{segment.partida.date}}
                                    </div>

                                </div>
                            </div>
                            <div class="time">
                                <div class="acronym">
                                    <strong>{{segment.partida.iata}}</strong>
                                    <span class="tooltip">
                                        {{segment.partida.airportShort }}
                                        <span class="tooltiptext">{{segment.partida.airportLong }}</span>
                                    </span>
                                </div>
                                <div class="hour">
                                    {{segment.partida.time}}
                                </div>
                                <div class="travel-time">
                                    <strong>{{segment.duracao}}</strong>
                                    <span class="tooltip">
                                        <span v-if="segment.numeroConexao == 1">1 conexão</span>
                                        <span v-if="segment.numeroConexao > 1">{{segment.numeroConexao}} conexões</span>
                                        <div v-if="segment.numeroConexao >= 1" class="tooltipbox">
                                            <ul>
                                                <li v-for="(conexao, sIndex) in segment.conexoes">
                                                    <div class="going">
                                                        <div>
                                                            <span>{{conexao.numeroVoo}}</span>
                                                            <div>{{conexao.date}}</div>
                                                        </div>
                                                    </div>

                                                    <div class="time">
                                                        <div class="acronym">
                                                            <strong>{{conexao.partida.iata}}</strong>
                                                            <span>
                                                                {{conexao.partida.airportLong }}
                                                            </span>
                                                        </div>
                                                        <div class="hour">
                                                            {{conexao.partida.time}}
                                                        </div>
                                                        <div class="travel-time">
                                                            <strong>
                                                                {{conexao.duracao}}
                                                            </strong>
                                                        </div>
                                                        <div class="hour">
                                                            {{conexao.chegada.time}}
                                                        </div>
                                                        <div class="acronym">
                                                            <strong>{{conexao.chegada.iata}}</strong>
                                                            <span>
                                                                {{conexao.chegada.airportLong}}
                                                            </span>
                                                        </div>

                                                    </div>
                                                </li>
                                            </ul>

                                        </div>
                                    </span>
                                </div>
                                <div class="hour">
                                    {{segment.chegada.time}}
                                </div>
                                <div class="acronym">
                                    <strong>{{segment.chegada.iata}}</strong>
                                    <span class="tooltip">
                                        {{segment.chegada.airportShort}}
                                        <span class="tooltiptext">{{segment.chegada.airportLong}}</span>
                                    </span>
                                </div>

                            </div>

                            <div v-if="segment.bagagemInclusa" class="baggage">
                                <img src="<?= asset("aereo/img/baggage.png"); ?>" alt="">
                                <span>Bagagem <br />inclusa</span>
                            </div>

                            <div v-if="segment.bagagemInclusa == false" class="baggage">
                                <img src="<?= asset("aereo/img/baggage.png"); ?>" alt="">
                                <span>Sem <br />bagagem</span>
                            </div>
                        </div>
                    </div>

                    <div class="general-datails">
                        <button type="button" class="vermais" v-on:click="toggle($event, 'flight')">Mostrar mais</button>
                        <div class="value">

                            <span class="tooltip">
                                {{fligth.valor.moeda}} {{fligth.valor.total}}
                                <span class="tooltipbox">
                                    <ul>
                                        <li v-for="pax in fligth.valor.porTipoPax">
                                            <span>Total por {{pax.tipo}}: <b>{{pax.valor}}</b></span>
                                        </li>
                                    </ul>
                                </span>
                            </span>

                        </div>

                        <label class="button-select">
                            <button type="button" v-on:click="selectFlight(fligth)">Selecionar</button>
                        </label>

                    </div>

                    <div class="flight-content">
                        <h4>Regras tarifárias</h4>
                        <div>
                            <div>
                                <h5>Cancelamento</h5>
                                <p>Não é reembosável</p>
                            </div>
                            <div>
                                <h5>Alterações</h5>
                                <p>Permite alterações cobrando multa e diferença tarifária</p>
                            </div>
                            <div>
                                <h5>Marcação de assento</h5>
                                <p>Regras tarifárias Cancelamento Não é reembolsável. Alterações Permite alterações cobrando multa e diferença tarifária. Marcação de assento Não permite a marcação de assento. Para marcar assento nos voos, é necessário entrar em contato diretamente com a companhia aérea, seja por telefone ou no balcão da empresa no momento do Checkin</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


            <div class="sidebar" ref="compra">
                <div class="side">
                    <input type="hidden" name="fligth" :value="rateToken" />
                    <button :disabled="rateToken==''">Comprar</button>
                    <h2>Seu pacote</h2>
                    <ul class="list">
                        <li><span><i class="fa-solid fa-user"></i></span> <strong><span v-if="numberAdults>0">{{numberAdults}} Adulto(s)</span><span v-if="numberAdults>0 && numberChildren>0"> e </span> <span v-if="numberChildren>0">{{numberChildren}} Criança(s)</span></strong></li>
                        <li v-for="trecho in trechos">
                            <span><i class="fa-solid fa-calendar-days"></i></span>
                            <strong> Embarque | </strong> {{formatDateBrazil(trecho.embarque)}} <br>
                            {{trecho.airportOrigem}} - {{trecho.airportDestino}}
                            <span v-if="airType == 'roundTrip'"> <strong> Volta | </strong>{{formatDateBrazil(trecho.volta)}}</span>
                        </li>
                    </ul>
                    <div id="sidebar-info">
                        <ul>
                            <li v-for="tipoPax in porTipoPax"><span>Por {{tipoPax.tipo}}</span><strong>{{moeda}} {{tipoPax.valor}}</strong></li>
                        </ul>
                        <div id="sidebar-amount">
                            <ul>
                                <li class="amount-dv">
                                    <strong>Valor final para</strong>
                                    <span><strong>{{moeda}} {{value}}</strong></span>
                                </li>
                                <li class="amount-tt"><span>{{numberAdults+numberChildren}} viajante<span v-if="numberAdults+numberChildren > 1">s</span></span> <strong>{{moeda}} {{value}}</strong></li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div class="side" v-if="fligths_bkp.length > 0">

                    <h2>Filtros</h2>

                    <div>
                        <fieldset>
                            <legend>Por linhas aéreas</legend>

                            <div v-for="filtro in filtros.filterCias">
                                <input name="filtro_cia" class="innput_filter" type="checkbox" :value="filtro" checked="checked" /> {{filtro}}
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Por bagagem</legend>

                            <div>
                                <input name="filtro_bag" class="innput_filter" type="checkbox" :value="0" checked="checked" /> Com bagagem
                            </div>
                            <div>
                                <input name="filtro_bag" class="innput_filter" type="checkbox" :value="1" checked="checked" /> Sem bagagem
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Por quantidade de paradas</legend>

                            <div v-for="filtro in filtros.filterStops">
                                <input name="filtro_stops" class="innput_filter" type="checkbox" :value="filtro" checked="checked" /> {{filtro}}
                            </div>
                        </fieldset>


                        <div><button v-on:click="AtualizarFiltro($event)"> Filtrar</button></div>
                    </div>

                </div>
            </div>


            <div class="sidebar">

            </div>
        </div>
    </form>
</div>

<?php $v->start("scripts"); ?>
<script src="<?= asset("aereo/js/main.js"); ?>"></script>
<?php $v->end(); ?>