const app = Vue.createApp({
    data() {
        return {
            //dados
            airType: 'roundTrip',
            trechos: [
                {
                    originDestiny: false,
                    boarding: false,
                    boardingVolta: false,
                    airportOrigem: '',
                    airportDestino: '',
                    embarque: '',
                    volta: '',
                    chamouAutoComplete: false
                }
            ],
            passengers: false,
            numberAdults: 1,
            numberChildren: 0,
            childAge: [],

            //erros MODAL
            modalValidation: false,
            errors: [],

            //RESULTS
            fligths: [],
            fligths_bkp: [],
            rateToken: '',
            value: '',
            moeda: '',
            porTipoPax: [],
            filtros: [],

        }
    },
    methods: {

        toggle(event, parent = false) {
            event.target.classList.toggle('open');

            if (parent) {
                event.target.closest('.flight').classList.toggle('open');
            }
        },

        manterTrecho(event, remover = false, todos = false) {
            event.preventDefault();
            if (remover) {
                if (todos) {
                    while (this.trechos.length > 1) {
                        this.trechos.pop();
                    }
                } else {
                    this.trechos.pop();
                }
            } else {
                this.trechos.push({
                    originDestiny: false,
                    boarding: false,
                    boardingVolta: false,
                    airportOrigem: '',
                    airportDestino: '',
                    embarque: '',
                    volta: '',
                    chamouAutoComplete: false
                });

            }
        },

        callAvailability(event) {
            event.preventDefault();

            document.getElementById('ajax_load').style.visibility = "visible";

            this.trechos.map(function (trecho, i) {
                trecho.origin = app.$refs.IOri[i].value;
                trecho.destiny = app.$refs.iDes[i].value;
            });

            var jsonObj = {
                adultos: this.numberAdults,
                criancas: this.numberChildren,
                trechos: this.trechos,
                airType: this.airType
            }

            for (var index in this.childAge) {
                jsonObj["childsAge_" + index] = this.childAge[index];
            }

            fetch('api/disponibilidade', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(jsonObj)
            }).then(function (response) {
                var contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    return response.json()
                } else {
                    alert("Oops, Tivemos um problema ao recuperar a lista de voos! " + contentType);
                }
            }).then(function (data) {
                if (data == null) {
                    alert("Oops, Tivemos um problema ao recuperar a lista de voos! ");
                    return;
                }
                if (data.success) {
                    callBackSuccess(data);
                } else {
                    callBackError(data.errors);
                }

            }).catch(function (error) {
                callBackError([{ mensagem: error }]);
            }).finally(function () {
                document.getElementById('ajax_load').style.visibility = "hidden";
            });
        },

        selectFlight(flight) {

            this.value = flight.valor.total;
            this.moeda = flight.valor.moeda;
            this.porTipoPax = flight.valor.porTipoPax;
            this.rateToken = "";
            var rateToken = "";
            if (this.airType == "oneWay" || this.airType == "roundTrip") {
                Array.from(document.getElementsByName(flight.nome + "_Ida")).map(function (ele) {
                    if (ele.checked) {
                        if (rateToken != '' && rateToken != undefined) {
                            rateToken += ",";
                        }
                        rateToken += flight.seguimentos[ele.value].rateToken;
                    }
                });

                if (this.airType == "roundTrip") {
                    Array.from(document.getElementsByName(flight.nome + "_Volta")).map(function (ele) {
                        if (ele.checked) {
                            if (rateToken != '' && rateToken != undefined) {
                                rateToken += ",";
                            }
                            rateToken += flight.seguimentos[ele.value].rateToken;
                        }
                    });
                }
            }

            if (this.airType == "mult") {
                for (var i = 1; i < this.trechos.length; i++) {
                    Array.from(document.getElementsByName(flight.nome + "_Trecho-" + i)).map(function (ele) {
                        if (ele.checked) {
                            if (rateToken != '' && rateToken != undefined) {
                                rateToken += ",";
                            }
                            rateToken += flight.seguimentos[ele.value].rateToken;
                        }
                    });
                }
            }
            this.rateToken = rateToken;
            window.scrollTo(0, this.$refs.compra.offsetTop);
        },


        AtualizarFiltro(event) {
            event.preventDefault();
            document.getElementById('ajax_load').style.visibility = "visible";

            var airType = this.airType;
            var trechos = this.trechos;

            this.fligths = this.fligths_bkp.filter(function (flight) {

                flight.seguimentos = flight.seguimentos.filter(function (seguimento) {

                    //Compara se não se enquadra em algum filtro
                    var t = Array.from(document.getElementsByClassName("innput_filter")).some(function (ele) {
                        if (!ele.checked) {
                            if (ele.name == "filtro_cia" && seguimento.companyShort == ele.value) {
                                return true
                            }

                            if (ele.name == "filtro_stops" && seguimento.numeroConexao == ele.value) {
                                return true
                            }

                            if (ele.name == "filtro_bag" && seguimento.bagagemInclusa == (ele.value == 1)) {
                                return true
                            }
                        }
                        return false;
                    });
                    return !t;

                });

                if (airType == "oneWay" && flight.seguimentos.length > 0) {
                    return flight;
                }
                if (airType == "roundTrip" && flight.seguimentos.some(function (s) { return s.trecho == "Ida" }) && flight.seguimentos.some(function (s) { return s.trecho == "Volta" })) {
                    return flight;
                }

                if (airType == "mult") {
                    var semTrechoVazio = true;
                    for (var i = 1; i < trechos.length; i++) {
                        semTrechoVazio = flight.seguimentos.some(function (s) { return s.trecho == "Trecho-" + i });
                        if (!semTrechoVazio) {
                            break;
                        }
                    }
                    if (semTrechoVazio) {
                        return flight;
                    }
                }
            });
            document.getElementById('ajax_load').style.visibility = "hidden";
        },

        formatDateBrazil(date) {
            if (date) {
                let data = new Date(date);
                return ((data.getDate() + 1)) + "/" + ((data.getMonth() + 1)) + "/" + data.getFullYear();
            }
            return '';
        }

    },
    mounted() {
        document.getElementById('ajax_load').style.visibility = "hidden";
    },

    updated() {
        var countItens = this.$refs.iDes.length - 1;
        if (this.trechos[countItens].chamouAutoComplete === false) {

            autocomplete(this.$refs.iDes[countItens], 'destino', countItens);
            autocomplete(this.$refs.IOri[countItens], 'origem', countItens);

            this.trechos[countItens].chamouAutoComplete = true;
        }
    }

}).mount("#app");

function callBackError(erros) {
    app.modalValidation = true;
    app.errors = erros;
    app.fligths = [];
}

function callBackSuccess(result) {
    //RESULTS
    app.fligths = result.result;
    app.fligths_bkp = result.result;
    app.filtros = result.filtros;
    if (result.length == 0) {
        callBackError([{ mensagem: 'Nenhuma resultado encontrado com os critérios da busca!' }]);
    }
}

function autocomplete(inp, way, indice) {
    var currentFocus;
    inp.addEventListener('input', function (e) {
        var a,
            b,
            i,
            val = this.value;
        callList(val, way, this);
    });
    inp.addEventListener('keydown', function (e) {
        var x = document.getElementById(this.id + 'autocomplete-list');
        if (x) x = x.getElementsByTagName('div');
        if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) {
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) {
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });
    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = x.length - 1;
        x[currentFocus].classList.add('autocomplete-active');
    }
    function removeActive(x) {
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove('autocomplete-active');
        }
    }
    function closeAllLists(elmnt) {
        var x = document.getElementsByClassName('autocomplete-items');
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    function callList(val, way, _this) {
        fetch('api/aeroportos/' + val + '/direcao/' + way).then(function (
            response,
        ) {
            var contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                response.json().then(function (airports) {
                    closeAllLists();
                    if (!val) {
                        return false;
                    }
                    currentFocus = -1;
                    a = document.createElement('DIV');
                    a.setAttribute('id', _this.id + 'autocomplete-list');
                    a.setAttribute('class', 'autocomplete-items');
                    _this.parentNode.appendChild(a);
                    for (i = 0; i < airports.length; i++) {
                        var item = airports[i];
                        if (
                            item.nome.substr(0, val.length).toUpperCase() ==
                            val.toUpperCase() ||
                            item.iata.toUpperCase() == val.toUpperCase() ||
                            item.nome_cidade.substr(0, val.length).toUpperCase() ==
                            val.toUpperCase()
                        ) {
                            b = document.createElement('DIV');
                            if (item.iata.toUpperCase() == val.toUpperCase()) {
                                b.innerHTML += item.nome + ' - ' + item.nome_cidade;
                                b.innerHTML +=
                                    '<strong> (' + item.iata.toUpperCase() + ')</strong>';
                            } else if (
                                item.nome.substr(0, val.length).toUpperCase() ==
                                val.toUpperCase()
                            ) {
                                b.innerHTML +=
                                    '<strong>' + item.nome.substr(0, val.length) + '</strong>';
                                b.innerHTML +=
                                    item.nome.substr(val.length) + ' - ' + item.nome_cidade;
                                b.innerHTML += ' (' + item.iata.toUpperCase() + ')';
                            } else {
                                b.innerHTML += item.nome + ' - ';
                                b.innerHTML +=
                                    '<strong>' +
                                    item.nome_cidade.substr(0, val.length) +
                                    '</strong>';
                                b.innerHTML += item.nome_cidade.substr(val.length);
                                b.innerHTML += ' (' + item.iata.toUpperCase() + ')';
                            }
                            b.setAttribute('data-iata', item.iata.toUpperCase());
                            b.setAttribute('data-namecompleto', item.nome);

                            b.addEventListener('click', function (e) {
                                inp.value = this.getAttribute('data-iata');
                                if (way == 'origem') {
                                    app.trechos[indice].airportOrigem = this.getAttribute('data-namecompleto');
                                } else {
                                    app.trechos[indice].airportDestino = this.getAttribute('data-namecompleto');
                                }
                                closeAllLists();
                            });
                            a.appendChild(b);
                        }
                    }
                });
            } else {
                console.log(
                    'Oops, Tivemos um problema ao recuperar a lista de aeroportos! ' +
                    contentType,
                );
            }
        });
    }
    document.addEventListener('click', function (e) {
        closeAllLists(e.target);
    });
}
