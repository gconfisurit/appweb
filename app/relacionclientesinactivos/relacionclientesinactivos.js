var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();
}

function cambiarEstado(id, est) {

    Swal.fire({
        title: '¿Estas Seguro?',
        text: "¿De realizar el cambio de estado?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, cambiar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "relacionclientesinactivos_controlador.php?op=activarydesactivar",
                method: "POST",
                dataType: "json",
                data: {codclie: id, est: est},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function (data) {
                    SweetAlertSuccessLoading(data.mensaje);
                    $('#cliente_data').DataTable().ajax.reload();
                }
            });
        }
    })
}

//function listar
function listar() {
    let isError = false;
    tabla = $('#cliente_data').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax": {
            url: 'relacionclientesinactivos_controlador.php?op=listar',
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
                $("#tabla").show('');//MOSTRAMOS LA TABLA.
                if(!isError) SweetAlertLoadingClose();
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

init();