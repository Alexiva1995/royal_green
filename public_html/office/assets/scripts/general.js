// seccion de activacion manual
function activarUser(idproducto) {
    $('#userdelete').val(idproducto)
    $('#myModal').modal('show')
}

// fin seccion de activacion manual

// Seccion de editar usuarios
function activarPersonal() {
    $('.personal').attr('disabled', false)
    $('#botom').show('slow')
}

function cancelarPersonal() {
    $('.personal').attr('disabled', true)
    $('#botom').hide('slow')
}

function activarContacto() {
    $('.contacto').attr('disabled', false)
    $('.botom1').show('slow')
}

function cancelarContacto() {
    $('.contacto').attr('disabled', true)
    $('.botom1').hide('slow')
}

function activarSocial() {
    $('.social').attr('disabled', false)
    $('#botom2').show('slow')
}

function cancelarSocial() {
    $('.social').attr('disabled', true)
    $('#botom2').hide('slow')
}

function activarBanco() {
    $('.banco').attr('disabled', false)
    $('#botom3').show('slow')
}

function cancelarBanco() {
    $('.banco').attr('disabled', true)
    $('#botom3').hide('slow')
}

function activarPago() {
    $('.pago').attr('disabled', false)
    $('#botom4').show('slow')
}

function cancelarPago() {
    $('.pago').attr('disabled', true)
    $('#botom4').hide('slow')
}

function eliminarProducto(iduser, disponible) {
    $('#userdelete').val(iduser)
    $('#disponible').val(disponible)
    $('#modalRetiro').modal('show')
}

function totalRetiro(valor) {
    console.log(valor);
    let resul = valor
    let tmp = valor * 0.045
    resul = valor - tmp
    $('#total').val(resul)
}
// fin seccion de editar usuarios

// seccion de login
function toggle() {
    $('.inicio').toggle('slow')
    $('.recuperar').toggle('slow')
}

function validarEdad() {
    var tmpedad = $('#edad').val()
    var hoy = new Date();
    var cumpleanos = new Date(tmpedad);

    var edad = hoy.getFullYear() - cumpleanos.getFullYear();
    var m = hoy.getMonth() - cumpleanos.getMonth();

    if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
        edad--;
    }

    if (edad < 18) {
        document.getElementById("btn").disabled = true;
        document.getElementById("errorEdad").style.display = 'block';
    } else {
        document.getElementById("btn").disabled = false;
        document.getElementById("errorEdad").style.display = 'none';
    }
}
// fin seccion login

// seccion index
function updatePaqueteInfo(paquete) {
    paquete = JSON.parse(paquete)
    let fecha = new Date(paquete.created_at)
    let fecha_string = fecha.getFullYear()+'/'+(fecha.getMonth()+1)+'/'+fecha.getDate()
    let progre = new Intl.NumberFormat('de-DE').format(paquete.progreso * 2)
    $('.indicate').css('display', 'none')
    $('#imgpaquete').attr('src', paquete.img)
    $('#userpaquete').html(paquete.iduser)
    $('#ganaciaPaquete').html(new Intl.NumberFormat('de-DE').format(paquete.ganado))
    $('#pogrepaquete').css('width', paquete.progreso+'%')
    $('#porcepaquete').html((progre))
    $('#activepaquete').html(fecha_string)
    $('#paquete'+paquete.id).css('display', 'block')
}

// option datatable
$(document).ready(function () {
    if ($("#mytable").length > 0) {
        $('#mytable').DataTable({
            dom: 'flBrtip',
            responsive: true,
            order: [[0, 'desc']],
            buttons: [
                'csv', 'pdf', 'print', 'excel'
            ]
        });
    }
});
// fin option datatable
// fin seccion index

// seccion global
function copyToClipboard(element) {
    var aux = document.createElement("input");
    aux.setAttribute("value", document.getElementById(element).innerHTML.replace('&amp;', '&').trim());
    document.body.appendChild(aux);
    aux.select();
    document.execCommand("copy");
    document.body.removeChild(aux);
    Swal.fire({
        title: '¡Link Copiado!',
        text: "Su link de referido esta listo para pegar",
          type: "success",
        confirmButtonClass: 'btn btn-primary',
        buttonsStyling: false,
    })
}

/**
* Permite modificar el lado binario donde se van a ir registrando los usuarios
*/
function updateSideBinary(value) {
    let url = route('change.side')
    let valor = value
    let data = {
        ladoregistrar: valor,
        _token: window.csrf_token,
    }
    let lado = (valor == 'D') ? 'Derecha' : 'Izquierda'
    $.post(url, data, function(response){
        if (response = 1) {
            Swal.fire({
            title: 'Lado Matrix Actualizado',
            text: "Su nuevo lado de registro binario es por la "+ lado,
            type: "success",
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false,
        }).then((value) => {
            if (value) {
                window.location.reload()
            }
        })
        }else{
            Swal.fire({
            title: 'Error',
            text: "No se pudo actualizar el lado a registrar intente de nuevo",
            type: "danger",
            confirmButtonClass: 'btn btn-primary',
            buttonsStyling: false,
        }).then((value) => {
            if (value) {
                window.location.reload()
            }
        })
        }
    })
}
// fin seccion global

// seccion de productos
function editProduct(dataProduct) {
    $('#price').val(dataProduct.meta_value)
    $('#content').val(dataProduct.post_content)
    $('#name').val(dataProduct.post_title)
    $('#visible').val(dataProduct.visible)
    $('#product').val(dataProduct.ID)
    $('#bono_binario').val((dataProduct.bono_binario * 100))
    $('#myModalEdit').modal('show')
}
// fin seccion de produtos

// seccion tienda 
function detalles(product, abono) {
    $('#idproducto').val(product.ID)
    $('#img').attr('src',product.imagen)
    $('#title').html(product.post_title)
    $('#title2').val(product.post_title)
    $('#content').html(product.post_content)
    $('#price').html('$ '+product.meta_value)
    $('#price2').val(product.meta_value)
    $('#abono').val(abono)
    $('#pagarcompra').click()
    // $('#myModal1').modal('show')
}
// fin seccion tienda

// seccion wallet
$(document).ready(function () {
    $('.retirarbtn').click(function () {
        console.log('entre');
        retirarpago()
        $('.formretiro').submit();
    })
})

function metodospago() {
    $('#correo').hide()
    $('#wallet').hide()
    $('#bancario').hide()
    let url = 'wallet/obtenermetodo/' + $('#metodopago').val()
    $.get(url, function (response) {
        let data = JSON.parse(response)
        $('#total').val(0)
        if (data.tipofeed == 1) {
            $('#comision').val(data.feed * 100)
            $('#lblcomision').text('Comision de Retiro en Porcentaje')
            $('#comisionH').val(data.feed)
            $('#tipo').val(data.tipofeed)
            $('#monto_min').val(data.monto_min)
        } else {
            $('#comision').val(data.feed)
            $('#lblcomision').text('Comision de Retiro Fija')
            $('#comisionH').val(data.feed)
            $('#tipo').val(data.tipofeed)
            $('#monto_min').val(data.monto_min)
        }
        if (data.correo == 1) {
            $('#correo').show()
        }
        if (data.wallet == 1) {
            $('#wallet').show()
        }
        if (data.bancario == 1) {
            $('#bancario').show()
        }
        $('#retirar').show()
    })
}

function retirarpago() {
    $('.formretiro').submit();
}
// fin seccion wallet

// seccion de admin remover billetera
function remover(id, total, restante) {
    let newDispo = (total - restante)
    $('#idtransh').html(id)
    $('#idtransi').val(id)
    $('#restante').html(restante)
    $('#newdispo').html(newDispo)
    $('#myModal2').modal('show')
}

// seccion de admin editar la billetera
function editWallet(iduser, accion) {
    $('#accion').val(accion)
    $('#iduser').val(iduser)
    $('#myModal2').modal('show')
}