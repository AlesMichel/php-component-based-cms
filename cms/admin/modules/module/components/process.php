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
//        $component->updateModuleTableFields();
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
        foreach ($components as $c) {
            $componentName = $c['name'];
            $componentIsMultlang = $c['multilang'];
            $componentFieldName = "component_$componentName";
            $componentId = $c['component_id'];

            if($componentIsMultlang == 1) {
                $componentData = $_POST[$componentFieldName];
                $componentDataEn = $_POST[$componentFieldName];
                $component->saveComponentData($componentName, $componentData, $componentDataEn);
            }else{
                $componentData = $_POST[$componentFieldName];
                //hash pass
                if($componentId == 6){$componentData = password_hash($componentData, PASSWORD_BCRYPT);}
                $component->saveComponentData($componentName, $componentData, null);
            }
        }
    }else if($action == "updateData"){
        $instance = $_SESSION['id'];
        $components = $component->getModuleComponents();
        foreach ($components as $c) {
            $componentName = $c['name'];
            $componentIsMultlang = $c['multilang'];
            $componentFieldName = "component_$componentName";
            $componentFieldNameEN = "component_en_$componentName";
            $componentId = $c['component_id'];

            if ($componentIsMultlang == 1) {
                $componentData = $_POST[$componentFieldName];
                $componentDataEn = $_POST[$componentFieldName];
                $component->saveComponentData($componentName, $componentData, $componentDataEn, $instance);
            } else {
                $componentData = $_POST[$componentFieldName];
                if($componentId == 6){$componentData = password_hash($componentData, PASSWORD_BCRYPT);
                }
                $component->saveComponentData($componentName, $componentData, null, $instance);
            }
        }
    }else if($action == "deleteData"){
        $instance = $_SESSION['id'];
        $components = $component->getModuleComponents();
        foreach ($components as $c) {
            $componentName = $c['name'];
            $component->deleteComponentData($instance);
        }

    }

}