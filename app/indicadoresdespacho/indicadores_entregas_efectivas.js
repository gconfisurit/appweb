
function entregas_efectivas(data, condicion_visibilidad_mes)
{
    let labels, values, value_max, promedio;

    if(!jQuery.isEmptyObject(data)) {
        //titulos de las barras
        labels = data.tabla.map( val => { return condicion_visibilidad_mes ? val.nombre_mes : val.fecha_entrega; });

        //valores de las barras
        values = data.tabla.map( val => { return parseInt(val.cant_documentos); });

        //obtiene el valor mas alto de los pedidos despachados
        value_max = Math.max(data.tabla.map( val => { return parseInt(val.ped_despachados); }));

        //obtiene un array de valores con el valor promedio
        promedio = values.map(() => { return parseFloat(data.promedio_diario_despacho.replace(',', '.')); });
    } else {
        labels = [];
        values = [];
        value_max = 0;
        promedio = 0;
    }

    // retornamos un objeto con el contenido necesario
    // para procesar el grafico
    return {
        labels : labels,
        value_max  : value_max,
        content: [
            {
                label      : 'Cantidad Pedidos entregados',
                type       : 'bar',
                color      : 'rgba(60,141,188,0.8)',
                pointRadius: false,
                fill       : false,
                values     : values
            }, {
                label      : 'Promedio',
                type       : 'line',
                color      : 'rgba(255,99,71,0.9)',
                pointRadius: true,
                fill       : false,
                values     : promedio
            }
        ]
    };
}

function thead_table_efectivas(incluye_ordenes)
{
    thead_ordenes = (!incluye_ordenes) ? '<th align="center" class="align-middle">Orden(es) Despacho</th>' : '';

    return '' +
        '<tr>' +
        '<th align="center" class="align-middle">Fecha Entrega</th>' +
        '<th align="center" class="align-middle">Pedidos Despachados</th>' +
        '<th align="center" class="align-middle">% Efectividad</th>' +
        thead_ordenes +
        '</tr>'
}

