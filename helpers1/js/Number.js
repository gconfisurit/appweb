
Number.prototype.format_money = function(n, x, s, c) {
    /**
     * Number.prototype.format(n, x, s, c)
     *
     * @param integer n: length of decimal
     * @param integer x: length of whole part
     * @param mixed   s: sections delimiter
     * @param mixed   c: decimal delimiter
     */
    const re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

function rounding2decimal(number) {
    let float = parseFloat(number);
    return Math.round(float * 100) / 100;
}

function addZeros(fact, cant_numbers = 6){
    let cad_cero = "";
    for(let i=0; i<(cant_numbers-fact.length); i++)
        cad_cero+=0;
    return cad_cero+fact;
}

function sum(arr_number) {
    let acum = 0;
    arr_number.forEach( val => { acum += val; });
    return acum;
}

function incremento_porcentual_ventas(arr_ventas) {
    if (arr_ventas.length > 2) {
        const valor_final = arr_ventas[arr_ventas.length-2].valor;
        const valor_inicial = arr_ventas[arr_ventas.length-3].valor;
        //Incremento porcentual = (Valor final – Valor inicial)/Valor inicial *100
        return (valor_final-valor_inicial)/valor_inicial*100;
    }
    return 0;
}

function is_int (mixedVar) {
     //   example 1: is_int(23)
     //   returns 1: true
     //   example 2: is_int('23')
     //   returns 2: false
     //   example 3: is_int(23.5)
     //   returns 3: false
     //   example 4: is_int(true)
     //   returns 4: false
    return mixedVar === +mixedVar && isFinite(mixedVar) && !(mixedVar % 1)
}

function is_float (mixedVar) {
     //   example 1: is_float(186.31)
     //   returns 1: true
    return +mixedVar === mixedVar && (!isFinite(mixedVar) || !!(mixedVar % 1))
}