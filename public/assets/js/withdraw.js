var vm_withdraw = new Vue({
    el: '#withdraw',
    data: function(){
        return {
            wallet: '',
            idliquidacion: 0
        }
    },

    methods: {
        /**
         * Permite abrir la modal de Detalle de informacion
         */
        openModalDetails: function(){
            $('#modalInfo').modal('show');
        },

        /**
         * Permite abrir la modal de Detalle de informacion
         */
         openModalAprobar: function(){
            $('#modalInfo').modal('hide');
            $('#modalModalAprobar').modal('show');
        },

        /**
         * Permite enviar el codigo de correo para aprobar liquidacion
         */
        sendCodeEmail: function (){
            let url = route('send-code-email', this.wallet);
            axios.get(url, []).then((response) => {
                if (response.data > 0) {
                    this.idliquidacion = response.data
                    toastr.success("Codigo Enviado, Revise su correo", '¡Genial!', { "progressBar": true });
                }else{
                    toastr.error("El monto solicitado es menor al minimo permitido 50$", '¡Error!', { "progressBar": true });    
                }
            }).catch(function (error) {
                toastr.error("Ocurrio un problema con la solicitud", '¡Error!', { "progressBar": true });
            })
        }
    }
})