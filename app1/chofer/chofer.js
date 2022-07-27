
var tabla;

//Función que se ejecuta al inicio
function init() {
	listar();

	//cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
	$("#chofer_form").on("submit", function (e) {
		guardaryeditar(e);
	})

	//cambia el titulo de la ventana modal cuando se da click al boton
	$("#add_button").click(function () {

		//habilita los campos cuando se agrega un registro nuevo ya que cuando se editaba un registro asociado entonces aparecia deshabilitado los campos
		$("#cedula").attr('disabled', false);
		$("#nomper").attr('disabled', false);

		$(".modal-title").text("Agregar Chofer");

	});
}

/*funcion para limpiar formulario de modal*/
function limpiar() {
	$("#cedula").val("");
	$('#nomper').val("");
	$('#estado').val("");
	$('#id_chofer').val("");
	$('.modal-title').text("Crear Chofer");
}

//function listar
function listar() {
	let isError = false;
	tabla = $('#choferes_data').dataTable({
		"aProcessing": true,//Activamos el procesamiento del datatables
		"aServerSide": true,//Paginación y filtrado realizados por el servidor
		"ajax":
			{
				url: 'chofer_controlador.php?op=listar',
				type: "get",
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
				url: "chofer_controlador.php?op=activarydesactivar",
				method: "POST",
				data: { id: id, est: est },
				success: function (data) {
					$('#choferes_data').DataTable().ajax.reload();
				}
			});
		}
	})
}

function mostrar(id_chofer = -1) {
	let isError = false;
	limpiar();
	$('#choferModal').modal('show');
	//si es -1 el modal es crear usuario nuevo
	if(id_chofer !== -1)
	{
		$.ajax({
			url: "chofer_controlador.php?op=mostrar",
			method: "POST",
			dataType: "json",
			data: {id_chofer: id_chofer},
			beforeSend: function () {
				SweetAlertLoadingShow();
			},
			error: function (e) {
				isError = SweetAlertError(e.responseText, "Error!")
				console.log(e.responseText);
			},
			success: function (data) {
				$('#cedula').val(data.cedula);
				$("#cedula").prop("disabled", true);
				$('#nomper').val(data.desccripcion);
				$("#nomper").prop("disabled", false);
				$('#estado').val(data.estatus);
				$('.modal-title').text("Editar Chofer");
			},
			complete: function () {
				if(!isError) SweetAlertLoadingClose();
			}
		});
	}

}

function guardaryeditar(e) {

	e.preventDefault(); //No se activará la acción predeterminada del evento
	const formData = new FormData($("#chofer_form")[0]);

	$.ajax({
		url: "chofer_controlador.php?op=guardaryeditar",
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
				$('#chofer_form')[0].reset();
				$('#choferModal').modal('hide');
				$('#choferes_data').DataTable().ajax.reload();
				limpiar();
			}

		}
	});
}


function eliminar(id, chofer) {

	Swal.fire({
		// title: '¿Estas Seguro?',
		text: "¿Estas Seguro de Eliminar el chofer "+chofer+" ?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, eliminar!',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if (result.value) {
			$.ajax({
				url: "chofer_controlador.php?op=eliminar",
				method: "POST",
				dataType: "json",
				data: {id: id},
				error: function (e) {
					SweetAlertError(e.responseText, "Error!")
					console.log(e.responseText);
				},
				success: function (data) {
					ToastSweetMenssage(data.icono, data.mensaje);
					$('#choferes_data').DataTable().ajax.reload();
				}
			});
		}
	})
}


init();






