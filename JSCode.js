// JavaScript source code
xButtonValue = 0;

$('#yField').focus();
$('textarea').focus(function () { $(this).css('background', '#191970') });
$('textarea').blur(function () { $(this).css('background', '#4169E1') });


function validate(form) {
    fail = checkFieldValue(form.yData.value, "Y", [-5, 5]);
    fail += checkFieldValue(form.rData.value, "R", [1, 4]);
    if (fail == "") 
        sendRequest();
    else
        alert(fail);
}

function checkFieldValue(str, valueType, range) {
    if (str == "")
        return "Не введено значение " + valueType + "\n";
    else if (isNaN(parseInt(str))) 
        return "Поле " + valueType + " может содержать только цифры\n";
    else if (parseInt(str) < range[0] || parseInt(str) > range[1])
        return "Значение " + valueType + " выходит за пределы допустимого диапазона ["+ range[0] + ";" + range[1] +"]\n";
    return "";
}

function press(button) {
    xButtonValue = button.value;
}

function sendRequest() {
    $.ajax({
        url: 'script1.php',
        method: 'get',
        cache: false,
        dataType: 'json',
        data: {
            "xVal": xButtonValue,
            "yVal": $('#yField').val(),
            "rVal": $('#rField').val(),
        },
        success: function (data, text, xhr) {
            result = data;
            $('#inData tr:last').after('<tr><th>' + result.curTime + '<th>' + result.scriptTime + '<th>' + result.isIn);
            $('#inXYR tr:last').after('<tr> <th>' + result.x + '<th>' + result.y + '<th>' + result.r);
            $('tr.log').html('Статус ответа сервера: ' + text + '<br /> Код ответа сервера: ' + xhr.status);
        },
        error: function (jqXHR) {
            $('tr.log').html('Статус ответа сервера: ' + jqXHR.status + '<br /> Код ответа сервера: ' + jqXHR.statusText);
        }
    });
}