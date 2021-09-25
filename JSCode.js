// JavaScript source code
xButtonValue = 0;

$('#yField').focus();
$('textarea').focus(function () { $(this).css('background', '#191970') });
$('textarea').blur(function () { $(this).css('background', '#4169E1') });

window.addEventListener('load', pageOpened, false);

function validate(form) {
    fail = checkFieldValue(form.yData.value, "Y", [-5, 5]);
    fail += checkFieldValue(form.rData.value, "R", [1, 4]);
    if (fail == "") {
        xRange = [-4, 4];
        yRange = [-5, 5];
        rRange = [1, 4];
        sendRequestForPoint(xRange, yRange, rRange);
    }
    else
        alert(fail);
}

function checkFieldValue(str, valueType, range) {
    if (str == "")
        return "Не введено значение " + valueType + "\n";
    else if (!/^-?\d+([.,]\d+)?$/.test(str))
        return "Поле " + valueType + " может содержать только цифры\n";
    else if (parseInt(str) <= range[0] || parseInt(str) >= range[1])
        return "Значение " + valueType + " выходит за пределы допустимого диапазона ["+ range[0] + ";" + range[1] +"]\n";
    return "";
}

function press(button) {
    $(`[value = ${xButtonValue}]`).css('background', '#4169E1')
    $(button).css('background', '#191970');
    xButtonValue = button.value;
}

function sendRequestForPoint(xRange, yRange, rRange) {
    $.ajax({
        url: 'script1.php',
        method: 'get',
        cache: false,
        dataType: 'json',
        data: {
            "isForSesData": 0,
            "xVal": xButtonValue,
            "xRange": xRange,
            "yVal": $('#yField').val(),
            "yRange": yRange,
            "rVal": $('#rField').val(),
            "rRange": rRange
        },
        success: function (data, text, xhr) {
            parseData(data, text, xhr);
            $('tr.log').html(`Статус ответа сервера: ${text} <br /> Код ответа сервера: ${xhr.status}`);
        },
        error: function (jqXHR) {
            $('tr.log').html(`Статус ответа сервера: ${jqXHR.status} <br /> Код ответа сервера: ${jqXHR.statusText}`);
        }
    });
}

function pageOpened() {
    $.ajax({
        url: 'script1.php',
        method: 'get',
        cache: false,
        dataType: 'json',
        data: {
            "isForSesData": 1
        },
        success: function (data) {
            for (i = 0; i < data.length; i++)
                parseData(data[i]);
        },
        error: function (jqXHR) {
            $('tr.log').html(`Статус ответа сервера: ${jqXHR.status} <br /> Код ответа сервера: ${jqXHR.statusText}`);
        }
    });
}

function parseData(data) {
    $('#inData tr:last').after(`<tr><th> ${data.curTime} <th> ${data.scriptTime} <th> ${data.isIn}`);
    $('#inXYR tr:last').after(`<tr> <th> ${data.x} <th> ${data.y} <th> ${data.r}`);

}