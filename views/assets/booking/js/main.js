const app = Vue.createApp({
  data() {
    return {
      availability: {},
      flightBookings: [],
      flightBookingsFields: {
        name: '',
        lastName: '',
        email: '',
        gender: '',
        documentType: '',
        document: '',
        birthDate: '',
        phone: '',
        country: '',
        zipCode: '',
        state: '',
        city: '',
        street: '',
        number: '',
        complement: '',
      },
      theTariffToken: '',
      validateErrors: [],
      showValidation: false,
      tariff: {},
    };
  },

  methods: {
    toTariff: (availability) => {
      var requestOptions = {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(availability),
      };
      fetch('tarifar', requestOptions)
        .then((response) => response.text())
        .then((result) => {
          document.getElementById('ajax_load').style.visibility = "hidden";
          let { success, data } = JSON.parse(result);

          if (success) {
            app.theTariffToken = data.data.priceGroup.segments[0].rateToken;
            app.tariff = data.data;
            return;
          } else {
            alert(
              'Houve um erro ao fazer a tarifação, verifique os dados e tente novamente.',
            );
          }
          window.location.href;
        })
        .catch((error) => {
          console.log(
            'Houve um erro ao fazer a tarifação, tente novamente mais tarde: ',
            error,
          );
        });
    },

    booking(event) {
      event.preventDefault();

      if (!app.handleValidation(this.flightBookings)) {
        app.showValidation = true;
        return;
      }

      document.getElementById('ajax_load').style.visibility = "visible";

      let bookingList = {
        bookings: [],
        availability: app.availability,
        tariff: app.tariff,
      };
      for (let index = 0; index < this.flightBookings.length; index++) {
        bookingList.bookings.push(this.flightBookings[index]);
      }

      var requestOptions = {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(bookingList),
      };

      fetch('salvar', requestOptions)
        .then((response) => response.text())
        .then((result) => {
          document.getElementById('ajax_load').style.visibility = "hidden";
          let { success } = JSON.parse(result);

          if (success) {
            alert('Booking salvo com sucesso.');
            return;
          } else {
            alert(
              'Houve um erro ao salvar o booking, verifique os dados e tente novamente.',
            );
          }
        })
        .catch((error) => {
          console.log(
            'Houve um erro ao salvar o booking, tente novamente mais tarde: ',
            error,
          );
        });
    },

    /**
     * @description Validates an array of bookings
     * @param {*} flightBookings
     * @returns boolean
     */
    handleValidation(flightBookings) {
      this.validateErrors = [];
      let fields = Object.getOwnPropertyNames(this.flightBookingsFields);
      for (let j = 0; j < fields.length; j++) {
        let field = fields[j];
        for (let k = 0; k < flightBookings.length; k++) {
          if (flightBookings[k][`${field}`] === '') {
            this.validateErrors.push({
              field: app.getFieldNameInPtBr(field),
              message: `O campo ${app.getFieldNameInPtBr(
                field,
              )} do(s) passageiro(s) é obrigatório.`,
            });
            break;
          }
        }
      }
      return this.validateErrors.length === 0 ? true : false;
    },

    /**
     * @description Returns the form field obtained by parameter in PT-BR
     * @param {*} value
     * @returns string
     */
    getFieldNameInPtBr(value) {
      switch (value) {
        case 'name':
          return 'Nome';
        case 'lastName':
          return 'Sobrenome';
        case 'email':
          return 'E-mail';
        case 'gender':
          return 'Gênero';
        case 'documentType':
          return 'Tipo Documento';
        case 'document':
          return 'Número Documento';
        case 'birthDate':
          return 'Data Nascimento';
        case 'phone':
          return 'Telefone';
        case 'country':
          return 'País';
        case 'zipCode':
          return 'CEP';
        case 'state':
          return 'Estado';
        case 'city':
          return 'Cidade';
        case 'street':
          return 'Rua';
        case 'number':
          return 'Número';
        case 'complement':
          return 'Complemento';
        default:
          return 'Campos';
      }
    },
  },

  mounted() {

    this.availability = JSON.parse(this.$refs.responseData.textContent);

    for (let index = 0; index < parseInt(this.availability.adultos); index++) {
      this.flightBookings.push({
        peopleType: 'Adulto',
        ...this.flightBookingsFields,
      });
    }

    for (let index = 0; index < parseInt(this.availability.criancas); index++) {
      this.flightBookings.push({
        peopleType: 'Criança',
        ...this.flightBookingsFields,
      });
    }

    document.getElementById('ajax_load').style.visibility = "hidden";
  },

  updated() {
    /**
     * @description: Copy the address for other passengers
     */
    setTimeout(() => {
      if (
        this.flightBookings[0].hasOwnProperty('country') &&
        this.flightBookings[0].country !== '' &&
        this.flightBookings[0].hasOwnProperty('zipCode') &&
        this.flightBookings[0].zipCode !== '' &&
        this.flightBookings[0].hasOwnProperty('state') &&
        this.flightBookings[0].state !== '' &&
        this.flightBookings[0].hasOwnProperty('city') &&
        this.flightBookings[0].city !== '' &&
        this.flightBookings[0].hasOwnProperty('street') &&
        this.flightBookings[0].street !== '' &&
        this.flightBookings[0].hasOwnProperty('number') &&
        this.flightBookings[0].number !== ''
      ) {
        this.flightBookings.forEach((item, index) => {
          if (
            index > 0 &&
            (!item.hasOwnProperty('country') || item.country === '') &&
            (!item.hasOwnProperty('zipCode') || item.zipCode === '') &&
            (!item.hasOwnProperty('state') || item.state === '') &&
            (!item.hasOwnProperty('city') || item.city === '') &&
            (!item.hasOwnProperty('street') || item.street === '') &&
            (!item.hasOwnProperty('number') || item.number === '')
          ) {
            item.country = this.flightBookings[0].country;
            item.zipCode = this.flightBookings[0].zipCode;
            item.state = this.flightBookings[0].state;
            item.city = this.flightBookings[0].city;
            item.street = this.flightBookings[0].street;
            item.number = this.flightBookings[0].number;
          }
        });
      }
    }, 10000);
  },

  watch: {
    availability(newAvailability) {
      this.toTariff(newAvailability);
    },
    theTariffToken(newTariffToken) {
      this.flightBookings.forEach((item) => {
        item.theTariffToken = newTariffToken;
      });
    },
  },
}).mount('#app');
