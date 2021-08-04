var vm_category = new Vue({
    el: '#category',
    data: function(){
        return{
            Category: [],
            Route: ''
        }
    },
    methods:{
        /**
         * Permite obtener la informacion de una categoria
         * @param {integer} id 
         */
        getEditData: function (id) {
            let url = route('group.edit', id)
            axios.get(url).then((response) => {
                this.Category = response.data
                this.Route = route('group.update', this.Category.id)
                if (this.Category.description != null) {
                    $('#summernoteEdit').summernote('pasteHTML', this.Category.description)
                }
                $('#modalEditCategories').modal('show')
            }).catch(function (error) {
                toastr.error("Ocurrio un problema con la solicitud", '¡Error!', { "progressBar": true });
            })
        },
        /**
         * Permite borrar una categoria
         * @param {integer} id 
         */
        deleteData: function(id){
            Swal.fire({
                title: "Advertencia",
                text: "Esta seguro que quieres eliminar el grupo "+id,
                type: "warning",
                confirmButtonClass: 'btn btn-primary',
                buttonsStyling: false,
            }).then(function(result){
                if (result.value) {
                    $('#delete'+id).submit()
                }
            });
        }
    }
})