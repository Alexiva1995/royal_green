var vm_adminService = new Vue({
    el: '#adminServices',
    data: function(){
        return{
            Service: [],
            Route: '',
            Option:{
                service_type: '',
                type: ''
            },
            Description: ''
        }
    },
    methods:{
        /**
         * Permite obtener la informacion de un servicio
         * @param {integer} id 
         */
        getEditData: function (id) {
            let url = route('package.edit', id)
            axios.get(url).then((response) => {
                this.Service = response.data
                this.Route = route('package.update', this.Service.id)
                if (this.Service.description != null) {
                    $('#summernoteEdit').summernote('pasteHTML', this.Service.description)
                }
                $('#modalEditServices').modal('show')
            }).catch(function (error) {
                toastr.error("Ocurrio un problema con la solicitud", '¡Error!', { "progressBar": true });
            })
        },
        /**
         * Permite borrar un servicio
         * @param {integer} id 
         */
        deleteData: function(id){
            Swal.fire({
                title: "Advertencia",
                text: "Esta seguro que quieres eliminar el servicio "+id,
                type: "warning",
                confirmButtonClass: 'btn btn-primary',
                buttonsStyling: false,
            }).then(function(result){
                if (result.value) {
                    $('#delete'+id).submit()
                }
            });
        },

        /**
         * Permite obtener la informacion del servicio
         * @param {integer} id 
         */
        getDescription: function(id){
            let url = route('package.show', id)
            axios.get(url).then((response) => {
                this.Description = response.data
                $('#modalDescriptionServices').modal('show')
            }).catch((error) => {
                toastr.error("Ocurrio un problema con la solicitud", '¡Error!', { "progressBar": true });
            })
        },

        /**
         * Permite aplicar el filtro selecionado
         */
        aplicFiltro: function(){
            $('#filtro').submit();
        }

    }
})