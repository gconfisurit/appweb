var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_tipodespacho();
    listar_tipodoc();
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#tipodespacho").val("");
    $("#tipodoc").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== ""
        && $("#tipodespacho").val() !== "" && $("#tipodoc").val() !== "")
        ? estado_minimizado = true : estado_minimizado = false ;
};

function listar_tipodespacho() {
    $.ajax({
        url: "devolucionessinmotivo_controlador.php?op=listar_tipodespacho",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_tipodespacho)){
                //lista de seleccion
                $('#tipodespacho').append('<option name="" value="">--Seleccione--</option>');
                $.each(data.lista_tipodespacho, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#tipodespacho').append('<option name="" value="' + opt.id +'">' + opt.desrip + '</option>');
                });
            }
        }
    });
}

function listar_tipodoc() {
    $.ajax({
        url: "devolucionessinmotivo_controlador.php?op=listar_tipodoc",
        method: "POST",
        dataType: "json",
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (data) {
            if(!jQuery.isEmptyObject(data.lista_tipodoc)){
                //lista de seleccion
                $('#tipodoc').append('<option name="" value="">--Seleccione--</option>');
                $.each(data.lista_tipodoc, function(idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#tipodoc').append('<option name="" value="' + opt.id +'">' + opt.desrip + '</option>');
                });
            }
        }
    });
}

$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
    $("#tipodespacho").change( () => no_puede_estar_vacio() );
    $("#tipodoc").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    var fechai = $("#fechai").val();
    var fechaf = $("#fechaf").val();
    var tipodespacho = $("#tipodespacho").val();
    var tipodoc = $("#tipodoc").val();

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (fechai !== "" && fechaf !== "" && tipodespacho !== "" && tipodoc !== "") {
            sessionStorage.setItem("fechai", fechai);
            sessionStorage.setItem("fechaf", fechaf);
            sessionStorage.setItem("tipodespacho", tipodespacho);
            sessionStorage.setItem("tipodoc", tipodoc);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#devolucionessinmotivo_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "devolucionessinmotivo_controlador.php?op=buscar_devolucionessinmotivo",
                    type: "post",
                    dataType: "json",
                    data: {fechai: fechai, fechaf: fechaf, tipodespacho: tipodespacho, tipodoc: tipodoc},
                    beforeSend: function () {
                        SweetAlertLoadingShow();
                    },
                    error: function (e) {
                        isError = SweetAlertError(e.responseText, "Error!")
                        console.log(e.responseText);
                    },
                    complete: function () {
                        if(!isError) SweetAlertLoadingClose();
                        $("#tabla").show('');//MOSTRAMOS LA TABLA.
                        validarCantidadRegistrosTabla();
                        mostrar()
                        limpiar();//LIMPIAMOS EL SELECTOR.
                    }
                },//TRADUCCION DEL DATATABLE.
                "bDestroy": true,
                "responsive": true,
                "bInfo": true,
                "iDisplayLength": 10,
                "order": [[0, "desc"]],
                'columnDefs':[
                    {
                        'visible': false,
                        'targets': [0]
                    }
                ],
                "language": texto_español_datatables
            });
            estado_minimizado = true;
        }
    } else {
        SweetAlertError('Debe seleccionar un rango de fecha y un vendedor.');
        return (false);

    }
});

function guardarCausaRechazoSeleccionada(num, tipo, numeror, op, key) {

    const motivo = $('#causa' + key).val();

    $.ajax({
        url: `devolucionessinmotivo_controlador.php?op=insertar_motivo`,
        dataType: "json",
        type: "POST",
        data: {num_nota : num, tipo_nota : tipo, numeror : numeror, op : op, motivo : motivo},
        error: function(e){
            $('#causa'+key).val('');
            SweetAlertError(e.responseText, "Error!")
        },
        success: function (data) {
            let {icono, mensaje} = data;
            ToastSweetMenssage(icono, mensaje)

            if (!icono.includes('error'))
                $('#devolucionessinmotivo_data').DataTable().ajax.reload();
            else
                $('#causa'+key).val('');
        }
    });
}

function mostrar() {

    var texto= 'Total de Devoluciones:  ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var tipodespacho = sessionStorage.getItem("tipodespacho", tipodespacho);
    var tipodoc = sessionStorage.getItem("tipodoc", tipodoc);
    if (fechai !== "" && fechaf !== "" && tipodespacho !== "" && tipodoc !== "") {
        window.location = "devolucionessinmotivo_excel.php?&fechai="+fechai+"&fechaf="+fechaf+"&tipodespacho="+tipodespacho+"&tipodoc="+tipodoc;
    }
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    var tipodespacho = sessionStorage.getItem("tipodespacho", tipodespacho);
    var tipodoc = sessionStorage.getItem("tipodoc", tipodoc);
    if (fechai !== "" && fechaf !== "" && tipodespacho !== "" && tipodoc !== "") {
        window.open('devolucionessinmotivo_pdf.php?&fechai='+fechai+'&fechaf='+fechaf+'&tipodespacho='+tipodespacho+"&tipodoc="+tipodoc, '_blank');
    }
});

init();
