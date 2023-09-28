const app = Vue.createApp({
  data() {
    return {
      idVoo: null,
      detalhesVoo: {},
      modalDetails: false,

      //erros MODAL
      modalValidation: false,
      errors: [],
    };
  },

  methods: {

    openFlight(event, idVoo) {
      this.idVoo = idVoo;
      document.getElementById('ajax_load').style.visibility = "visible";

      fetch('detalhes', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ "idvoo": idVoo })
      }).then(function (response) {
        var contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
          return response.json()
        } else {
          callBackError([{ mensagem: "Oops, Tivemos um problema ao recuperar a reserva! " + contentType }]);
        }
      }).then(function (data) {
        if (data == null) {
          callBackError([{ mensagem: "Oops, Tivemos um problema ao recuperar a lista de voos! " }]);
          return;
        }
        if (data.success) {
          callBackSuccess(data.result);
        } else {
          callBackError(data.errors);
        }

      }).catch(function (error) {
        callBackError([{ mensagem: error }]);
      }).finally(function () {
        document.getElementById('ajax_load').style.visibility = "hidden";
      });
    },

    marcarAssento(bookingToken, legId) {

      document.getElementById('ajax_load').style.visibility = "visible";

      fetch('assentos/{{bookingToken}}/{{legId}}', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ "idvoo": idVoo })
      }).then(function (response) {
        var contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
          return response.json()
        } else {
          callBackError([{ mensagem: "Oops, Tivemos um problema ao recuperar a reserva! " + contentType }]);
        }
      }).then(function (data) {
        if (data == null) {
          callBackError([{ mensagem: "Oops, Tivemos um problema ao recuperar a lista de voos! " }]);
          return;
        }
        if (data.success) {
          callBackSuccess(data.result);
        } else {
          callBackError(data.errors);
        }

      }).catch(function (error) {
        callBackError([{ mensagem: error }]);
      }).finally(function () {
        document.getElementById('ajax_load').style.visibility = "hidden";
      });
    },

    cancelar(event, code) {
      document.getElementById('ajax_load').style.visibility = "visible";
      fetch('cancelarbilhete/' + code, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      }).then(function (response) {
        var contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
          return response.json()
        } else {
          alert("Oops, Tivemos um problema ao cancelar o bilhete! " + contentType);
        }
      }).then(function (data) {
        if (data == null) {
          alert("Oops, Tivemos um problema ao cancelar o bilhete! ");
          return;
        }
        if (data.success) {
          openFlight(event, this.idVoo);
        } else {
          callBackError(data.errors);
        }

      }).catch(function (error) {
        callBackError([{ mensagem: error }]);
      }).finally(function () {
        document.getElementById('ajax_load').style.visibility = "hidden";
      });
    },


    cancelarReserva(event) {
      document.getElementById('ajax_load').style.visibility = "visible";
      fetch('cancelar/' + code, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      }).then(function (response) {
        var contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
          return response.json()
        } else {
          alert("Oops, Tivemos um problema ao cancelar o bilhete! " + contentType);
        }
      }).then(function (data) {
        if (data == null) {
          alert("Oops, Tivemos um problema ao cancelar o bilhete! ");
          return;
        }
        if (data.success) {
          openFlight(event, this.idVoo);
        } else {
          callBackError(data.errors);
        }

      }).catch(function (error) {
        callBackError([{ mensagem: error }]);
      }).finally(function () {
        document.getElementById('ajax_load').style.visibility = "hidden";
      });
    },



    formatDateTimeBrazil(date) {
      if (date) {
        let data = new Date(date);
        return ((data.getDate())) + "/" + ((data.getMonth() + 1)) + "/" + data.getFullYear() + ' ' + data.getHours() + 'h' + data.getMinutes() + 'm';
      }
      return '';
    },

    formatMoney(atual) {
      return atual.toLocaleString('pt-br', { minimumFractionDigits: 2 });
    }

  },

  mounted() {
    document.getElementById('ajax_load').style.visibility = "hidden";
  },

}).mount('#app');



function callBackError(erros) {
  app.modalValidation = true;
  app.errors = erros;
  app.fligths = [];

  app.modalDetails = false;
}

function callBackSuccess(result) {
  //RESULTS
  app.detalhesVoo = result;
  app.modalDetails = true;

}