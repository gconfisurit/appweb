var tabla;
var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = true;

    listar_marcas();
}

function limpiar() {
    $("#marcas").val("");
}

var no_puede_estar_vacio = function () {
    ($(".marcas").val().length > 0) ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $(".marcas").change(() => no_puede_estar_vacio());
});


function listar_marcas() {
    let isError = false;
    $.ajax({
        url: "disponiblealmacen_controlador.php?op=listar_marcas",
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
            if(!jQuery.isEmptyObject(data.lista_marcas)){
                //lista de seleccion de marcas
                $('#marcas').append('<option name="" value="Todos">Todos</option>');
                $.each(data.lista_marcas, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#marcas').append('<option name="" value="' +opt.marca +'">'+ opt.marca+ '</option>');
                });
            }
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

/*$(document).on("click", "#btn_consultar", function () {
    alert('si entra');
   
});*/

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    var marcas = $("#marcas").val();
    
    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (marcas !== "") {
            sessionStorage.setItem("marcas", marcas);
            
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#inventario_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "disponiblealmacen_controlador.php?op=buscar_inventario",
                    type: "post",
                    dataType: "json",
                    data: {marcas: marcas},
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
                        //mostrar()
                        //limpiar();//LIMPIAMOS EL SELECTOR.
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
        SweetAlertError('Debe seleccionar un rango de fecha.');
        return (false);

    }
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var marcas = sessionStorage.getItem("marcas");
    if (marcas !== "") {
        window.location = 'disponiblealmacen_excel.php?&marcas='+marcas;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var marcas = sessionStorage.getItem("marcas");
    if (marcas !== "") {
        window.open('disponiblealmacen_pdf.php?&'+marcas, '_blank');
    }
});

init();
