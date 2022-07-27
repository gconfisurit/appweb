var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_vendedores();
}

function limpiar() {
    $("#vendedor").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
    ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

function listar_vendedores() {
    let isError = false;
    $.ajax({
        url: "clientesbloqueados_controlador.php?op=listar_vendedores",
        method: "POST",
        dataType: "json",
        beforeSend: function () {
            SweetAlertLoadingShow();
        },
        error: function (e) {
            isError = SweetAlertError(e.responseText, "Error!")
            send_notification_error(e.responseText);
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_vendedores)){
                //lista de seleccion de vendedores
                $('#vendedor').append('<option name="" value="">Seleccione</option>');
                $.each(data.lista_vendedores, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#vendedor').append('<option name="" value="' + opt.CodVend +'">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

$(document).ready(function(){
    $("#vendedor").change( () => estado_minimizado = true )
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_clientesbloqueados", function () {

    var vendedor = $("#vendedor").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (vendedor !== "") {
            sessionStorage.setItem("vendedor", vendedor);
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            let isError = false;
            tabla = $('#clientesbloqueados_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "clientesbloqueados_controlador.php?op=buscar_clientesbloqueados",
                    type: "post",
                    dataType: "json",
                    data: {vendedor: vendedor},
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        send_notification_error(e.responseText);
                        console.log(e.responseText);
                    },
                    complete: function () {
                        if(!isError) SweetAlertLoadingClose();
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        validarCantidadRegistrosTabla();
                        mostrar();
                        limpiar();//LIMPIAMOS EL SELECTOR.

                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                "language": texto_español_datatables
            });
            estado_minimizado = true;
        }
    } else {
        SweetAlertError('Debe seleccionar al menos un Vendedor!');
        return (false);
    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.location = "clientesbloqueados_excel.php?vendedor="+vendedor;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var vendedor = sessionStorage.getItem("vendedor");
    if (vendedor !== "") {
        window.open('clientesbloqueados_pdf.php?&vendedor='+vendedor, '_blank');
    }
});

function mostrar() {

    var texto= 'Clientes Bloqueados: ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
