<?php
function Sanitize(){
    foreach ($_GET as $item){
        $item = strip_tags($item);
        $item = htmlentities($item);
        $item = stripslashes($item);
    }
}

function Validate($val, $range){
    $fail = "";
    if (strlen($val) == 0)
        $fail .= "Empty value : $val";
    elseif (!is_numeric($val) == 1)
        $fail .= "Ucorrect symbols: $val";
    elseif ((float)$val <= $range[0] || (float)$val >= $range[1])
        $fail .= "Value out of range: $val";

    if ($fail != "")
        GetBadReq($fail);
}


function GetBadReq($fail){
    header('X-PHP-Response-Code: 400',true, 400);
    echo $fail;
    exit;
}

function GetPoint(){

    Validate($_GET['xVal'], [-4, 4]);
    Validate($_GET['yVal'],[-5, 5]);
    Validate($_GET['rVal'], [1, 4]);

    $nms = array(
    'x' => $_GET["xVal"],
    'y' => $_GET["yVal"],
    'r' => $_GET["rVal"]
    );

    $quat = "I";

    if ($nms['x'] < 0 && $nms['y'] > 0)
        $quat = "II";
    elseif ($nms['x'] < 0 && $nms['y'] < 0)
        $quat = "III";
    elseif ($nms['x'] > 0 && $nms['y'] < 0)
        $quat = "IV";
    elseif ($nms['x'] == 0 && $nms['y'] == 0)
        $quat = "0";

    $answer = "";

    switch($quat){
        case "I":
            if ($nms['x'] <= $nms['r'] && $nms['y'] <= $nms['r'])
                $answer = "Yes";
            else
                $answer = "No";
            break;
        case "II":
            if ($nms['x']**2 + $nms['y']**2 <= $nms['r']**2)
                $answer = "Yes";
            else
                $answer = "No";
            break;
        case "III":
            if (-2*$nms['r']*$nms['x'] + -$nms['r']*$nms['y'] <= $nms['r'])
                $answer = "Yes";
            else
                $answer = "No";
            break;
        case "IV":
            $answer = "No";
            break;
        case "0":
            $answer = "Yes";
            break;
    }


    $result = array(
        "x" => $nms['x'],
        "y" => $nms['y'],
        "r" => $nms['r'],
        "isIn" => $answer,
        );
    return $result;
}

$start = microtime(true);

//Sanitize();

session_start();
if (!isset($_SESSION["data"]))
    $_SESSION["data"] = array();

$tpe = $_GET["isForSesData"];

if ($tpe){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($_SESSION["data"]);
}
else{
    $point = GetPoint();

    $nowDate = microtime(true);
    $diff = round($nowDate - $start, 8);
    date_default_timezone_set('Europe/Moscow');
    $curTime = date("H:i:s");

    $point["curTime"] = $curTime;
    $point["scriptTime"] = $diff;

    array_push($_SESSION["data"], $point);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($point);
}
?>