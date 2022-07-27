var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();
    //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
    $("#rol_form").on("submit", function (e) {
        guardaryeditar(e);
    })

    //cambia el titulo de la ventana modal cuando se da click al boton
    $("#add_button").click(function () {

        //habilita los campos cuando se agrega un registro nuevo ya que cuando se editaba un registro asociado entonces aparecia deshabilitado los campos
        $("#rol").attr('disabled', false);


        $(".modal-title").text("Agregar Rol");

    });
}

/*funcion para limpiar formulario de modal*/
function limpiar() {
    $("#rol").val("");
    $('#id_rol').val("");
    $(".modal-title").text("Crear Rol");
}

//function listar
function listar() {
    let isError = false;
    tabla = $('#roles_data').dataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
        "aServerSide": true,//Paginación y filtrado realizados por el servidor
        "ajax":
            {
                url: 'roles_controlador.php?op=listar',
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


function mostrar(id_rol = -1) {
    let isError = false;
    limpiar();
    //si es -1 el modal es crear nuevo
    if(id_rol !== -1)
    {
        $.ajax({
            url: "roles_controlador.php?op=mostrar",
            method: "POST",
            dataType: "json",
            data:  {id_rol: id_rol},
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                console.log(e.responseText);
            },
            success: function (data) {
                //si existe la cedula_relacion entonces tiene relacion con otras tablas
                if (data.cedula_relacion) {

                    $('#rolModal').modal('show');
                    $('#rol').val(data.cedula_relacion);
                    //desactiva el campo

                    $('#rol').val(data.descripcion);
                    $("#rol").prop("disabled", false);

                    $('.modal-title').text("Editar Rol");
                    $('#id_rol').val(id_rol);

                } else {

                    $('#rolModal').modal('show');
                    $('#rol').val(data.descripcion);
                    $("#rol").prop("disabled", false);

                    $('.modal-title').text("Editar Rol");
                    $('#id_rol').val(id_rol);
                }
            },
            complete: function () {
                if(!isError) SweetAlertLoadingClose();
            }
        });
    }
}

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardaryeditar(e) {

    e.preventDefault(); //No se activará la acción predeterminada del evento
    var formData = new FormData($("#rol_form")[0]);

    $.ajax({
        url: "roles_controlador.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        error: function (e) {
            SweetAlertError(e.responseText, "Error!")
            console.log(e.responseText);
        },
        success: function (datos) {
            let { icono, mensaje } = datos;
            ToastSweetMenssage(icono, mensaje);

            //verifica si el mensaje de insercion contiene error
            if(mensaje.includes('error')) {
                return (false);
            } else {
                $('#rol_form')[0].reset();
                $('#rolModal').modal('hide');
                $('#roles_data').DataTable().ajax.reload();
                limpiar();
            }
        }
    });
}

function eliminar(id, rol) {

    Swal.fire({
        // title: '¿Estas Seguro?',
        text: "¿Estas Seguro de Eliminar el Rol "+rol+" ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "roles_controlador.php?op=eliminar",
                method: "POST",
                dataType: "json",
                data: {id_rol: id},
                error: function (e) {
                    SweetAlertError(e.responseText, "Error!")
                    console.log(e.responseText);
                },
                success: function (data) {

                    //verifica si el mensaje de insercion contiene error
                    if(data.icono.includes('error')) {
                        SweetAlertError(data.mensaje, "Error!")
                        return (false);
                    } else {
                        ToastSweetMenssage(data.icono, data.mensaje);
                        $('#rol_form')[0].reset();
                        $('#rolModal').modal('hide');
                        $('#roles_data').DataTable().ajax.reload();
                        limpiar();
                    }
                }
            });
        }
    })
}

//Mostrar datos del usuario en la ventana modal del formularioS
init();






