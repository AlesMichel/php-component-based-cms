<?php
include_once("../../../autoload.php");

$out = '';

//1. decide if creating new entries or editing
if($_SERVER ['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['method'];
    if($action == 'add'){
        //1. get all fields

    }
}





$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();
