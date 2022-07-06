<?php
include( "../inc/load.php");
$request = getRequest($_REQUEST);
if ($request['idextrusoraPresetup']) {
    $obj = new ExtrusoraPresetup($request['idextrusoraPresetup']);
    $arrayPresetup = $obj->carregar();
    echo retornarJsonEncode(array_map('utf8_encode', $arrayPresetup));
}