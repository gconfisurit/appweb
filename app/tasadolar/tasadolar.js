var tabla;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    listar();
}

function validarCantidadRegistrosTabla() {
    (tabla.rows().count() === 0)
        ? estado = true  : estado = false ;
    $('#btn_excel').attr("disabled", estado);
    $('#btn_pdf').attr("disabled", estado);
}

//function listar
function listar() {
    let isError = false;
    tabla = $('#tasa_data').dataTable({

        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax": {
            url: 'tasadolar_controlador.php?op=listar',
            type: "get",
            dataType: "json",
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
                validarCantidadRegistrosTabla();
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,//Por cada 10 registros hace una paginación
        "order": [[0, "asc"]],//Ordenar (columna,orden)
        "language": texto_español_datatables
    }).DataTable();
}

//ACCION AL PRECIONAR EL BOTON EXCEL.
$(document).on("click","#btn_excel", function(){
        window.location = "tasadolar_excel.php";
});

//ACCION AL PRECIONAR EL BOTON PDF.
$(document).on("click","#btn_pdf", function(){
        window.open('tasadolar_pdf.php', '_blank');
});

init();
