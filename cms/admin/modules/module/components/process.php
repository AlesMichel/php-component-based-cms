<?php
include_once("../../../../autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"] ?? null;
    $moduleId = $_SESSION["current_module_id"] ?? null;

    if($action == "create" and $moduleId != null){

        //if component is unique
        //create entry in module_components
        //add column in module table




    }



}