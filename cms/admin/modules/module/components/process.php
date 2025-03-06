<?php
include_once("../../../../autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $moduleName = $_SESSION["module_name"];
    $action = $_POST["action"];

    if($action == "create"){


        $component = new componentCommon($moduleName);
        //if component is unique
        //create entry in module_components
        //add column in module table
        $componentName = $_POST["component_name"];
        $componentId = $_SESSION["component_id"];
        $componentIsMultlang = $_POST["component_isMultlang"];
        $componentIsRequired = $_POST["component_isRequired"];

        $component->initComponent($componentName, $componentId, $componentIsMultlang, $componentIsRequired);

        var_dump($component->initComponent($componentName, $componentId, $componentIsMultlang, $componentIsRequired));






    }



}