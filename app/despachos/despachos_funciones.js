
/*************************************************************************************************************/
/*                                          GESTION DE DOCUMENTOS                                            */
/*************************************************************************************************************/

function anadir(documento) {
    if(documento.length > 0) {
        registros_por_despachar += (documento + ";");
    }
}

function eliminar(documento) {
    if(documento.length > 0) {
        $.ajax({
            url: "despachos_controlador.php?op=obtener_pesoporfactura",
            type: "POST",
            dataType: "json",
            data: {
                numero_fact: documento,
                peso_acum_facturas: peso_acum_facturas,
                peso_max_vehiculo: peso_max_vehiculo,
                cubicaje_acum_facturas: cubicaje_acum_facturas,
                cubicaje_max_vehiculo: cubicaje_max_vehiculo,
                eliminarPeso: "si"
            },
            error: function (e) {
                SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            },
            success: function (data) {
                //asignamos el peso acumulado restandole la factura a eliminar
                peso_acum_facturas = data.pesoNuevoAcum.toString();
                cubicaje_acum_facturas = data.cubicajeNuevoAcum.toString();

                //eliminamos la factura del string
                registros_por_despachar = registros_por_despachar.replace((documento + ";"), '');

                //seteamos la barra de progreso
                barraDeProgreso(data.bgProgreso, data.pesoNuevoAcum, data.porcentajePeso, data.cubicajeNuevoAcum, data.porcentajeCubicaje);

                //recargar la tabla
                cargarTabladeFacturasporDespachar();
            }
        });
    }
}

function barraDeProgreso(colorFondo, pesoAcumulado, porcentajePeso, cubicajeAcumlulado, porcentajeCubicaje){
    //modifica el texto de los kilos acumulados vs el maximo de carga
    $( "#textoBarraProgreso" )
        .removeClass(valor_bg_progreso)
        .addClass(colorFondo)
        .text(pesoAcumulado+" kg  /  "+peso_max_vehiculo+" kg"+"   ("+parseInt(porcentajePeso)+"%)");

    //modifica la bara de progreso del peso acumulado
    $("#barraProgreso")
        .removeClass(valor_bg_progreso)
        .addClass(colorFondo)
        .css('width', porcentajePeso+'%')
        .attr("aria-valuenow", porcentajePeso);

    //modifica el texto del cubicaje acumulado vs el maximo de volumen
    $( "#textoBarraProgresoCubicaje" )
        .text(cubicajeAcumlulado+" cm3  /  "+cubicaje_max_vehiculo+" cm3"+"   ("+parseInt(porcentajeCubicaje)+"%)");

    //modifica la bara de progreso del cubicaje acumulado
    $("#barraProgresoCubicaje")
        .css('width', porcentajeCubicaje+'%')
        .attr("aria-valuenow", porcentajeCubicaje);

    //guardamos el valor del background del peso acumulado
    valor_bg_progreso = colorFondo;
}