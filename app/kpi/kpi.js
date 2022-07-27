let estado_vacio;


//FUNCION QUE SE EJECUTA AL INICIO.
function init() {
    $("#loader").hide();
    $("#d_habiles").val(1);
    $("#d_transcurridos").val(0);
    estado_vacio = true;
}

function limpiar() {
    $("#fechai").val("");
    $("#fechaf").val("");
    $("#d_habiles").val(1);
    $("#d_transcurridos").val(0);
}

var no_puede_estar_vacio = function () {
    ($("#fechai").val() !== "" && $("#fechaf").val() !== ""
        && parseInt($("#d_habiles").val()) > 1 && parseInt($("#d_transcurridos").val()) > 0)
        ? estado_vacio = false : estado_vacio = true;
};

var calcular_proyeccion = function () {
    if(parseInt($("#d_habiles").val()) > 1
        && parseInt($("#d_transcurridos").val()) > 0
    ) {
        const tr = $("#d_transcurridos").val();
        const ha = $("#d_habiles").val();
        $("#proyeccion").val(rounding2decimal((parseFloat(tr)/parseFloat(ha))*100)+'%');
    } else {
        $("#proyeccion").val(0+'%');
    }
}

$(document).ready(function () {
    $("#fechai").change(() => no_puede_estar_vacio());
    $("#fechaf").change(() => no_puede_estar_vacio());
    $("#d_habiles").on('keyup', () => {
        no_puede_estar_vacio();
        calcular_proyeccion();
    }).keyup();
    $("#d_transcurridos").on('keyup', () => {
        no_puede_estar_vacio();
        calcular_proyeccion();
    }).keyup();
});


//ACCION AL PRECIONAR EL BOTON.
$(document).on("click", "#btn_consultar", function () {

    let fechai = $("#fechai").val();
    let fechaf = $("#fechaf").val();
    let d_habiles = parseInt($("#d_habiles").val());
    let d_transcurridos = parseInt($("#d_transcurridos").val());

    if (!estado_vacio && fechai !== "" && fechaf !== "" && d_habiles > 1 &&  d_habiles > 0) {
        window.open("kpi_tabla.php?&fechai="+fechai+"&fechaf="+fechaf+""+"&d_habiles="+d_habiles+"&d_trans="+d_transcurridos, '_blank');
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
        if (d_habiles <= 1) {
            Swal.fire('Atención!', 'Debe Ingresar Día Hábil mayor a 1!', 'error');
            return false;
        }
        if (d_habiles <= 0) {
            Swal.fire('Atención!', 'Debe Ingresar Días Transcurridos!', 'error');
            return false;
        }
    }
});

init();
