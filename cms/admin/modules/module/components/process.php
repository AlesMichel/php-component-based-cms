<?php
include_once("../../../../autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $moduleName = $_SESSION["module_name"];
    $action = $_POST["action"] ?? null;
    $component = new componentCommon($moduleName);

    if($action == "create"){
        //if component is unique
        //create entry in module_components
        //add column in module table
        $componentName = $_POST["component_name"];
        $componentId = $_POST["component_id"];
        $componentIsMultlang = $_POST["component_isMultlang"];
        $componentIsRequired = $_POST["component_isRequired"];
        $component->initComponent($componentName, $componentId,$componentIsMultlang, $componentIsRequired);
        $component->updateModuleTableFields();

    }else if($action == "update"){


    }else if($action == "delete"){
        $componentName = $_POST["component_name"];
        $success = $component->deleteComponent($componentName);
        if($success){
            $_SESSION["cms_message"] = "Komponenta byla smazana";
            header("location: ../index.php");
        }
    }






}