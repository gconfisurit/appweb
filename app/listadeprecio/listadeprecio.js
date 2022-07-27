var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = false;
    listar_depositos_marcas();
}

function limpiar() {
    $("#depo").val("");
    $("#marca").val("");
    $("#orden").val("");
    $("#p1").prop("checked", false);
    $("#p2").prop("checked", false);
    $("#p3").prop("checked", false);
    $("#iva").prop("checked", true);
    $("#cubi").prop("checked", false);
    $("#exis").prop("checked", true);
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true : estado = false;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

var no_puede_estar_vacio = function () {
    ($("#depo").val() !== "" && $("#marca").val() !== "" && $("#orden").val() !== "") ? estado_minimizado = true : estado_minimizado = false;
};

$(document).ready(function () {
    $("#depo").change(() => no_puede_estar_vacio());
    $("#marca").change(() => no_puede_estar_vacio());
    $("#orden").change(() => no_puede_estar_vacio());
});


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_listadeprecio", function () {

    var depos = $('#depo').val();
    var marcas = $('#marca').val();
    var orden = $('#orden').val();
    var p1 = 0;
    var p2 = 0;
    var p3 = 0;
    var bulto=0;
    var paquete=0;
    var iva = 1;
    var cubi = 0;
    var exis = 0;

    if (document.getElementById('p1').checked) { p1 = 1; }
    if (document.getElementById('p2').checked) { p2 = 1; }
    if (document.getElementById('p3').checked) { p3 = 1; }
    if (document.getElementById('iva').checked) { iva = 1.16; }
    if (document.getElementById('cubi').checked) { cubi = 1; }
    if (document.getElementById('exis').checked) { exis = 1; }
    if (document.getElementById('bulto').checked) { bulto = 1; }
    if (document.getElementById('paquete').checked) { paquete = 1; }

    if (estado_minimizado) {
        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
        if (depos !== "" && marcas !== "" && orden !== "") {
            sesionStorageItems(depos, marcas, orden, p1, p2, p3, iva, cubi, exis);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            $.ajax({
                async: true,
                url: "listadeprecio_controlador.php?op=listar",
                method: "POST",
                dataType: "json",
                data: {'depo': depos,'marca': marcas,'orden': orden,'p1': p1, 'p2': p2, 'p3': p3, 'iva': iva, 'cubi': cubi, 'exis':exis, 'bulto':bulto, 'paquete':paquete,},
                beforeSend: function () {
                    SweetAlertLoadingShow();
                    if(tabla instanceof $.fn.dataTable.Api){
                        $('#tablaprecios').DataTable().clear().destroy();
                    }
                    $('#tablaprecios thead').empty();
                },
                error: function (e) {
                    isError = SweetAlertError(e.responseText, "Error!")
                    send_notification_error(e.responseText);
                    console.log(e.responseText);
                },
                success: function (data) {
                    //creamos una matriz JSON para aoColumnDefs
                    var aryJSONColTable = [];
                    /*var aryColTableChecked = data.columns;
                    for (var i = 0; i <aryColTableChecked.length; i ++) {
                        aryJSONColTable.push ({
                            "sTitle": aryColTableChecked[i],
                            "aTargets": [i]
                        });
                    }*/
                    $.each(data.columns, function(i, opt) {
                        aryJSONColTable.push({"sTitle": opt, "aTargets": [i]});
                    });

                    tabla = $('#tablaprecios').DataTable({
                        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                        "sEcho": data.sEcho, //INFORMACION PARA EL DATATABLE
                        "iTotalRecords": data.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
                        "iTotalDisplayRecords": data.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
                        "data": data.aaData, // informacion por registro
                        //CodigoDinamico
                        "aoColumnDefs": aryJSONColTable,
                        "bProcessing": true,
                        "bLengthChange": true,
                        "bFilter": true,
                        "bScrollCollapse": true,
                        "bJQueryUI": true,
                        //finCodigoDinamico
                        "bDestroy": true,
                        "responsive": true,
                        "bInfo": true,
                        "iDisplayLength": 10,
                        "language": texto_español_datatables
                    });

                    validarCantidadRegistrosTabla();
                    limpiar();
                },
                complete: function () {
                    if(!isError) SweetAlertLoadingClose();
                    $("#tabla").show('');//MOSTRAMOS LA TABLA.
                }
            });

            estado_minimizado = true;
        }
    } else {

        if (depos === "") {
            SweetAlertError('Seleccione un Almacen!');
            return (false);
        }
        if (marcas === "") {
            SweetAlertError('Seleccione al menos una Marca!');
            return (false);
        }
        if (orden === "") {
            SweetAlertError(' Seleccione como desea Ordenar!');
            return (false);
        }
    }
});

function sesionStorageItems(depos, marcas, orden, p1, p2, p3, iva, cubi, exis){
    sessionStorage.setItem("depos", depos);
    sessionStorage.setItem("marcas", marcas);
    sessionStorage.setItem("orden", orden);
    sessionStorage.setItem("p1", p1);
    sessionStorage.setItem("p2", p2);
    sessionStorage.setItem("p3", p3);
    sessionStorage.setItem("iva", iva);
    sessionStorage.setItem("cubi", cubi);
    sessionStorage.setItem("exis", exis);
}

function listar_depositos_marcas(){
    let isError = false;
    $.ajax({
        url: "listadeprecio_controlador.php?op=listar_depositos_marcas",
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
            $('#depo').append('<option name="" value="">Seleccione Almacen</option>');
            $.each(data.lista_depositos, function(idx, opt) {
                //se itera con each para llenar el select en la vista
                $('#depo').append('<option name="" value="' + opt.codubi +'">'+ opt.codubi +': '+ opt.descrip.substr(0, 35) + '</option>');
            });

            $('#marca').append('<option name="" value="">Seleccione una Marca</option>').append('<option name="" value="-">TODAS</option>');
            $.each(data.lista_marcas, function(idx, opt) {
                $('#marca').append('<option name="" value="' + opt.marca +'">' + opt.marca + '</option>');
            });
        },
        complete: function () {
            if(!isError) SweetAlertLoadingClose();
        }
    });
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
    var depos = sessionStorage.getItem("depos");
    var marcas = sessionStorage.getItem("marcas");
    var orden = sessionStorage.getItem("orden");
    var p1 = sessionStorage.getItem("p1");
    var p2 = sessionStorage.getItem("p2");
    var p3 = sessionStorage.getItem("p3");
    var iva = sessionStorage.getItem("iva");
    var cubi = sessionStorage.getItem("cubi");
    var exis = sessionStorage.getItem("exis");
    window.location = "listadeprecio_excel.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis;
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var depos = sessionStorage.getItem("depos");
    var marcas = sessionStorage.getItem("marcas");
    var orden = sessionStorage.getItem("orden");
    var p1 = sessionStorage.getItem("p1");
    var p2 = sessionStorage.getItem("p2");
    var p3 = sessionStorage.getItem("p3");
    var iva = sessionStorage.getItem("iva");
    var cubi = sessionStorage.getItem("cubi");
    var exis = sessionStorage.getItem("exis");
    window.open("listadeprecio_pdf.php?&depos="+depos+"&marcas="+marcas+""+"&orden="+orden+"&p1="+p1+"&p2="+p2+"&p3="+p3+"&iva="+iva+"&cubi="+cubi+"&exis="+exis, '_blank');
});

init();
