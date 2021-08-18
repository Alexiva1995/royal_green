var vm_liquidation = new Vue({
    el: '#settlement',
    data: function(){
        return{
            seleAllComision: false,
            StatusProcess: '',
            ComisionesDetalles: [] 
        }
    },
    methods: {
        /**
         * Permite obtener la informacion de las comisiones de un usuario
         * @param {integer} iduser 
         */
        getDetailComision: function(iduser){
            let url = route('liquidation.show', iduser)
            this.seleAllComision = false
            axios.get(url).then((response) => {
                this.ComisionesDetalles = response.data
                $('#modalModalDetalles').modal('show')
            }).catch(function (error) {
                toastr.error("Ocurrio un problema con la solicitud", '¡Error!', { "progressBar": true });
            })
        },

        /**
         * Permite obtener la informacion de las comisiones de las liquidaciones
         * @param {integer} iduser 
         */
         getDetailComisionLiquidation: function(iduser){
            let url = route('liquidation.edit', iduser)
            this.seleAllComision = false
            axios.get(url).then((response) => {
                this.ComisionesDetalles = response.data
                $('#modalModalDetalles').modal('show')
            }).catch(function (error) {
                toastr.error("Ocurrio un problema con la solicitud", '¡Error!', { "progressBar": true });
            })
        },

        /**
         * Permite obtener la informacion de las comisiones de las -liquidaciones para aprobar o reversar
         * @param {integer} iduser
         * @param {string} status
         */
         getDetailComisionLiquidationStatus: function(iduser, status){
            this.StatusProcess = status
            let url = route('liquidation.edit', iduser)
            this.seleAllComision = false
            axios.get(url).then((response) => {
                this.ComisionesDetalles = response.data
                $('#modalModalAccion').modal('show')
            }).catch(function (error) {
                toastr.error("Ocurrio un problema con la solicitud", '¡Error!', { "progressBar": true });
            })
        }
    }
})