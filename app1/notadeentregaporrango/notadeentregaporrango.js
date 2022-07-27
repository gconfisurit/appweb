let estado_vacio;

//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    estado_vacio = true;
}

function limpiar() {
    $("#nrodocumento_inicial").val("");
    $("#nrodocumento_final").val("");
}

var no_puede_estar_vacio = function()
{
    ($("#nrodocumento_inicial").val() !== "" && $("#nrodocumento_final").val() !== "")
        ? estado_vacio = false : estado_vacio = true;
};

$(document).ready(function(){
    $("#nrodocumento_inicial").change( () => no_puede_estar_vacio() );
    $("#nrodocumento_final").change( () => no_puede_estar_vacio() );
});

//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    const documentoi = addZeros($("#nrodocumento_inicial").val());
    const documentof = addZeros($("#nrodocumento_final").val());

    if (!estado_vacio && documentoi !== "000000" && documentof !== "000000") {
        window.open("notadeentregaporrango_pdf.php?&documentoi="+documentoi+"&documentof="+documentof, "_blank");
        limpiar();
        estado_vacio = true;
    } else {

        if (documentoi === "000000") {
            Swal.fire('Atención!', 'Debe ingresar un Numero de Nota de Entrega inicial!', 'error');
            return false;
        }
        if (documentof === "000000") {
            Swal.fire('Atención!', 'Debe ingresar un Numero de Nota de Entrega final!', 'error');
            return false;
        }
    }
});

init();
