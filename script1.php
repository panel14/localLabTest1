<?php

function Sanitize(){
    foreach ($_GET as $item){
        $item = strip_tags($item);
        $item = htmlentities($item);
        $item = stripslashes($item);
    }
}

function Validate($arr){
    foreach($arr as $num){
        if (str_contains($num, "err")){                                 //заменить условие на регулярку, с запятой работает
            header('X-PHP-Response-Code: 400',true, 400);
            $result = array("error" => "Uncorrect data");
            echo json_encode($result);
            exit;
        }
    }
    return $arr;
}

$start = microtime(true);

Sanitize();

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
$nowDate = microtime(true);
$diff = round($nowDate - $start, 8);
date_default_timezone_set('Europe/Moscow');
$curTime = date("H:i:s");

$result = array(
    "x" => $nms['x'],
    "y" => $nms['y'],
    "r" => $nms['r'],
    "isIn" => $answer,
    "scriptTime" => $diff,
    "curTime" => $curTime
    );

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
?>