

function graficar(labels, values, highscore = 100, offset = 5, symbol = '', $id, tipo_grafica = 'line') {

    const ticksStyle = { fontColor: '#495057', fontStyle: 'bold' };
    const mode = 'index';
    const intersect = true;

    const chart = new Chart($id, {
        type: tipo_grafica,
        data: {
            labels: labels,
            datasets: [
                {
                    data                : [...values[0]],
                    backgroundColor     : 'transparent',
                    borderColor         : '#007bff',
                    pointBorderColor    : '#007bff',
                    pointBackgroundColor: '#007bff',
                    fill                : false
                }, {
                    data                : [...values[1]],
                    backgroundColor     : 'tansparent',
                    borderColor         : '#ced4da',
                    pointBorderColor    : '#ced4da',
                    pointBackgroundColor: '#ced4da',
                    fill                : false
                }]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                mode: mode,
                intersect: intersect
            },
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    // display: false,
                    gridLines: {
                        display: true,
                        lineWidth: '4px',
                        color: 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                    },
                    ticks: $.extend({
                        beginAtZero: true,
                        suggestedMax: highscore,
                        offset: offset,
                        // Include a dollar sign in the ticks
                        callback: function (value, index, values) {
                            /*if (value >= 1000) {
                                value /= 1000
                                value += 'k'
                            }*/
                            return '' + value.format_money(0, 3, '.', ',');
                        }
                    }, ticksStyle)
                }],
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    ticks: ticksStyle
                }]
            }
        }
    });

}

function get_values(data, cantidad_meses_evaluar) {
    let values_ventas = [];

    //array inicializado en 0 en base a la cantidad de meses a evaluar
    for (let i=1;i<cantidad_meses_evaluar;i++)
        values_ventas.push(parseFloat(0.0).toString());

    //llenamos el array con la data necesaria para ser procesada en el reporte.
    data.forEach( (value, index) => {
        const { num_mes, valor } = value;
        if (num_mes <= cantidad_meses_evaluar)
            values_ventas[parseInt(num_mes-1)] = valor;
    });

    return values_ventas;
}


