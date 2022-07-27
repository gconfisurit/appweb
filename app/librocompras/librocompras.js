let estado_vacio;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    estado_vacio = true;
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
}

var no_puede_estar_vacio = function()
{
    ($("#fechai").val() !== "" && $("#fechaf").val() !== "")
        ? estado_vacio = false : estado_vacio = true;
};

$(document).ready(function(){
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let fechai = $("#fechai").val();
    let fechaf = $("#fechaf").val();
console.log(estado_vacio)
    if (!estado_vacio && fechai !== "" && fechaf !== "") {
        window.open("librocompras_tabla.php?&fechai="+fechai+"&fechaf="+fechaf, '_blank');
        limpiar();
        estado_vacio = true;
    } else {

        if (fechai === "") {
            Swal.fire('Atención!', 'Debe Colocar la fecha inicial!', 'error');
            return false;
        }
        if (fechaf === "" ) {
            Swal.fire('Atención!', 'Debe Colocar la fecha final!', 'error');
            return false;
        }
    }
});

init();
