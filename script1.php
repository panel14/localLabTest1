<?php
function Sanitize(){
    foreach ($_GET as $item){
        $item = strip_tags($item);
        $item = htmlentities($item);
        $item = stripslashes($item);
    }
}

function Validate($arr){
    $fail = "";
    $ranges = [];
    $ranges[1] = $_GET["xRange"];
    $ranges[2] = $_GET["yRange"];
    $ranges[3] = $_GET["rRange"];

    $cnt = 1;
    foreach($arr as $num){
        $strNum = strval($num);
        echo $strNum;
        if ($num == "")
            $fail . "Empty value";
        elseif (!preg_match("/^-?\d+([.,]\d+)?$/u", $num))
            $fail . "Ucorrect symbols";
        elseif ($num <= $ranges[$cnt][0] || $num >= $ranges[$cnt][1])
            $fail . "Uncorrect range";

        if ($fail != "")
            GetBadReq();
        $cnt++;
    }
}


function GetBadReq(){
    header('X-PHP-Response-Code: 400',true, 400);
    exit;
}

function GetPoint(){
    $nms = array(
        'x' => $_GET["xVal"],
        'y' => $_GET["yVal"],
        'r' => $_GET["rVal"]
        );

    Validate($nms);

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