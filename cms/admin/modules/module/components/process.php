<?php
include_once("../../../../autoload.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $moduleName = $_SESSION["module_name"];
    $action = $_POST["action"] ?? null;
    $component = new componentCommon($moduleName);

    //actions for component management
    if ($action == "create") {
        $componentName = $_POST["component_name"];
        $componentId = $_POST["component_id"];
        $componentIsMultlang = $_POST["component_isMultlang"];
        $componentIsRequired = $_POST["component_isRequired"];
        $component->initComponent($componentName, $componentId,$componentIsMultlang, $componentIsRequired);
        $component->updateModuleTableFields();
    } else if ($action == "update") {
        //update
    } else if ($action == "delete") {
        $componentName = $_POST["component_name"];
        $componentIsMultlang = $_POST["component_isMultlang"];
        $success = $component->deleteComponent($componentName, $componentIsMultlang);
        if ($success) {
            $_SESSION["cms_message"] = "Komponenta byla smazana";
            header("location: ../index.php");
        }
    }
    //actions for data management
    else if ($action == "insert") {

        $components = $component->getModuleComponents();
        foreach ($components as $component) {
            $componentName = $component['name'];
            $componentIsMultlang = $component['multilang'];
            $componentFieldName = "component_$componentName";

            if ($componentIsMultlang == 1) {
                $componentData = $_POST[$componentFieldName];
            } else {
                $componentFieldName = "component_en_$componentName";
                $componentData = $_POST[$componentFieldName];
                $componentDataEn = $_POST[$componentFieldName];
            }

        }
    }
}