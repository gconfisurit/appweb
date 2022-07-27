
function oportunidad_despacho(data, condicion_visibilidad_mes)
{
    let labels, values, value_max, promedio, objetivo;

     if(!jQuery.isEmptyObject(data)) {
        //titulos de las barras
        labels = data.tabla.map( val => { return condicion_visibilidad_mes ? val.nombre_mes : val.fecha_desp; });

        //valores de las barras
        values = data.tabla.map( val => { return parseInt(val.oportunidad); });

        //obtiene el valor mas alto de los pedidos despachados
        value_max = 95/*Math.max(data.tabla.map( val => { return parseInt(val.oportunidad); }))*/;

        //obtiene un array de valores con el valor promedio
        promedio = values.map(() => { return parseFloat(data.oportunidad_promedio.replace(',', '.')); });

        //obtiene un array de valores con el valor objetivo
        objetivo = values.map(() => { return parseFloat(data.objetivo); });
    } else {
        labels = [];
        values = [];
        value_max = 0;
        promedio = 0;
        objetivo = 0;
    }

    // retornamos un objeto con el contenido necesario
    // para procesar el grafico
    return {
        labels : labels,
        value_max  : value_max,
        content: [
            {
                label      : '% Oportunidad Despacho',
                type       : 'bar',
                color      : 'rgba(60,141,188,0.8)',
                pointRadius: false,
                fill       : false,
                values     : values
            }, /*{
                label      : 'Promedio',
                type       : 'line',
                color      : 'rgba(83,109,254,0.9)',
                pointRadius: true,
                fill       : false,
                values     : promedio
            },*/ {
                label      : 'Objetivo',
                type       : 'line',
                color      : 'rgba(255,99,71,0.9)',
                pointRadius: true,
                fill       : false,
                values     : objetivo
            }
        ]
    };
}

function construirTablaOportunidadDespacho(data) {

    tabla = $('#oportunidad_data').DataTable({
        "aProcessing": true,//ACTIVAMOS EL PROCESAMIENTO DEL DATATABLE.
        "aServerSide": true,//PAGINACION Y FILTROS REALIZADOS POR EL SERVIDOR.
        "sEcho": data.sEcho, //INFORMACION PARA EL DATATABLE
        "iTotalRecords": data.iTotalRecords, //TOTAL DE REGISTROS AL DATATABLE.
        "iTotalDisplayRecords": data.iTotalDisplayRecords, //TOTAL DE REGISTROS A VISUALIZAR.
        "data": data.aaData, // informacion por registro
        "bDestroy": true,
        "responsive": false,
        "bInfo": true,
        "iDisplayLength": 10,
        // "order": [[0, "desc"]],
        'columnDefs':[{
            "targets": [0,1,2,3,4,5,6,7], // your case first column
            "className": "text-center"
        }],
        "language": texto_español_datatables
    });

}

function thead_table_oportunidad()
{
    return '' +
        '<tr>' +
        '<th align="center" class="align-middle">Fecha Despacho</th>' +
        '<th align="center" class="align-middle">Cantidad Documentos</th>' +
        '<th align="center" class="align-middle">% Oportunidad</th>' +
        '</tr>'
}