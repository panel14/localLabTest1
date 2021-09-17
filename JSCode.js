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
        return "�� ������� �������� " + valueType + "\n";
    else if (isNaN(parseInt(str))) 
        return "���� " + valueType + " ����� ��������� ������ �����\n";
    else if (parseInt(str) < range[0] || parseInt(str) > range[1])
        return "�������� " + valueType + " ������� �� ������� ����������� ��������� ["+ range[0] + ";" + range[1] +"]\n";
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
            $('tr.log').html('������ ������ �������: ' + text + '<br /> ��� ������ �������: ' + xhr.status);
        },
        error: function (jqXHR) {
            $('tr.log').html('������ ������ �������: ' + jqXHR.status + '<br /> ��� ������ �������: ' + jqXHR.statusText);
        }
    });
}