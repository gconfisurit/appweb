var tabla;

var estado_minimizado;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#tabla").hide();
    estado_minimizado = true;
    listar_vendedores();
}



function listar_vendedores() {
    let isError = false;
    $.ajax({
        url: "Fact_cobrar_controlador.php?op=listar_vendedores",
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
            if (!jQuery.isEmptyObject(data.lista_vendedores)) {
                //lista de seleccion de vendedores
                $('#vendedor').append('<option name="" value="">Seleccione</option>');
                $('#vendedor').append('<option name="" value="Todos">Todos</option>');
                $.each(data.lista_vendedores, function (idx, opt) {
                    //se itera con each para llenar el select en la vista
                    $('#vendedor').append('<option name="" value="' + opt.CodVend + '">' + opt.CodVend + ': ' + opt.Descrip.substr(0, 35) + '</option>');
                });
            }
        },
        complete: function () {
            if (!isError) SweetAlertLoadingClose();
        }
    })
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

/*var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "" )
        ? estado_minimizado = true : estado_minimizado = false ;
};


$(document).ready(function(){
    $("#fechai").change( () => no_puede_estar_vacio() );
    $("#fechaf").change( () => no_puede_estar_vacio() );
});*/

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {
    var vendedor = $("#vendedor").val();
  

        $("#tabla").hide();
        $("#minimizar").slideToggle();///MINIMIZAMOS LA TARJETA.
        estado_minimizado = false;
    sessionStorage.setItem("vendedor", vendedor);
            let isError = false;
            //CARGAMOS LA TABLA Y ENVIARMOS AL CONTROLADOR POR AJAX.
            tabla = $('#cobrar_data').DataTable({
                "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
                "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
                "ajax": {
                    url: "Fact_cobrar_controlador.php?op=buscar_facturasporcobrar",
                    type: "post",
                    dataType: "json",
                    data: { vendedor:vendedor},
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
    
});

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
   var fechai = sessionStorage.getItem("fechai", fechai);
   var fechaf = sessionStorage.getItem("fechaf", fechaf);

    window.location = "Fact_cobrar_excel.php?&fechai="+fechai+"&fechaf="+fechaf;

});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
    var fechai = sessionStorage.getItem("fechai", fechai);
    var fechaf = sessionStorage.getItem("fechaf", fechaf);
    if (fechai !== "" && fechaf !== "") {
        window.open('Fact_cobrar_pdf.php?&fechai='+fechai+'&fechaf='+fechaf,'_blank');
    }
});

function mostrar() {

    var texto= 'Clientes No Activados: ';
    var cuenta =(tabla.rows().count());
    $("#cuenta").html(texto + cuenta);
}

init();
