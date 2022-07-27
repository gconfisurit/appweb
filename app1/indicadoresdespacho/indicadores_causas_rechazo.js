
function rechazo_de_los_clientes(data, condicion_visibilidad_mes, causas)
{
    let labels, value_max, values;

    if(!jQuery.isEmptyObject(data)) {
        //titulos de las barras
        labels = data.tabla.map( val => { return condicion_visibilidad_mes ? val.nombre_mes : val.fecha_entrega; });

        //creamos un array con la data necesaria para ser procesada en el reporte.
        values = get_data(data, causas, labels);

        //obtiene el valor mas alto de la cantidad de rechazos
        value_max = Math.max(...data.tabla.map( val => { return Math.max(...val.observacion.map( val => { return val.cant; })); }) );
    } else {
        labels = [];
        values = [];
        value_max = 0;
    }

    // retornamos un objeto con el contenido necesario
    // para procesar el grafico
    return {
        labels : labels,
        value_max  : value_max,
        content: [
            ...values.map( (val, index) => {

                const {tipo, color, values} = val;
                const {r, g, b} = hexToRgb(color);

                return {
                    label      : tipo.toLowerCase(),
                    type       : 'bar',
                    color      : `rgba(${r}, ${g}, ${b}, 0.8)`,
                    pointRadius: false,
                    fill       : false,
                    values     : values
                };
            })
        ]
    };
}

function thead_table_rechazo(incluye_ordenes)
{
    thead_ordenes = (!incluye_ordenes) ? '<th align="center" class="align-middle">Orden(es) Despacho</th>' : '';

    return '' +
        '<tr>' +
        '<th align="center"  class="align-middle">Fecha Devolución</th>' +
        '<th align="center"  class="align-middle">Devoluciones</th>' +
        '<th align="center"  class="align-middle">% Rechazos</th>' +
        thead_ordenes +
        '</tr>'
}

function get_index_colors(data) {
    // creamos el array vacio
    var arr = [];

    // iteramos a data.tabla
    data.forEach( val => {
        // cada observacion tiene un array de diferentes dimensiones.
        // devolvemos especificamente los id de color del tipo de observacion (filtrando que no existan valores null, undefined, NaN)
        //agregamos con push al array fuera de forEach tomando encuenta que retornamos un array gracias al spread operator (...)
        arr.push(...val.observacion.map( val => { return val.color.id; }).filter(Boolean));
    });

    //retornamos un nuevo array sin valores repetidos y ordenados en forma ascendente.
    return [...new Set(arr)].sort((a, b) => a - b );
}

function get_data(data, causas, labels) {
    let values_rechaz = [];

    //array inicializado en 0 en base a la cantidad de registro de labels
    arr_temp = labels.map( () => { return 0; });

    //creamos un array con los id de colores por causa de rechazo disponibles en el rango de fecha
    index_color = get_index_colors(data.tabla);

    //creamos un array inicializado basandose en la dimension de index_color.
    index_color.forEach( (idx) => {
        values_rechaz.push( {id: idx, tipo: causas[idx-1].descripcion, values: [...arr_temp], color: causas[idx-1].color} );
    }); console.log(values_rechaz)

    //llenamos el array con la data necesaria para ser procesada en el reporte.
    const { tabla } = data;
    values_rechaz.forEach( (val, index) => {
        const { tipo } = val;
        tabla.forEach( (t, idx) => {
            const tipos_observacion = t.observacion.map( v => { return v.tipo.toUpperCase(); });

            if(tipos_observacion.includes(tipo)) {
                values_rechaz[index].values[idx] = t.observacion[tipos_observacion.indexOf(tipo)].cant;
            }
        });
    });

    return values_rechaz;
}

