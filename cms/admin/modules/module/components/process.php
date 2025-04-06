<?php
include_once("../../../../autoload.php");
include_once("../../../../src/Components/Image.php");

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
        $componentIsMultlang = $_POST["component_isMultlang"] ?? 0;
        $success = $component->deleteComponent($componentName, $componentIsMultlang);
        if ($success) {
            $_SESSION["cms_message"] = "Komponenta byla smazana";
            header("location: ../index.php");
        }
    }else if($action == "addOption"){
        $componentName = $_POST["component_name"];
        $newOption = $_POST['newOption'];
        $newOptionEn = $_POST['newOptionEn'] ?? null;
        echo $componentName, $newOptionEn, $newOption;
        if(!empty($newOption)){
            $component->insertOption($componentName, $newOption, $newOptionEn);
        }
    }
    //actions for data management
    else if ($action == "insert") {
        $components = $component->getModuleComponents();
        $instance = $component->getHighestInstance() + 1;
        foreach ($components as $c) {

            $getComponentName = $c['name'];
            $componentFieldName = "component_$getComponentName";
            $getComponentId = $c['component_id'];
            $getComponentIsMultlang = $c['multilang'];
            $getComponentIsRequired = $c['required'];
            $componentFieldNameEN = "component_en_$getComponentName";
            $getComponentData = $_POST[$componentFieldName] ?? null;
            $getComponentDataEn = $_POST[$componentFieldNameEN] ?? null;

                $path = componentCommon::buildPath($getComponentId);
                $moduleName = $component->getName();
                $component = new $path($moduleName,$getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang, $getComponentData, $getComponentDataEn);
                  $component->insertComponentData($getComponentName, $instance,
                    $componentData = $component->getDataFormated()[0],
                    $componentData = $component->getDataFormated()[1]
                );

            }
        header("location: ../../index.php");
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
                $componentDataEn = $_POST[$componentFieldNameEN];
                $component->updateComponentData($componentName, $instance,$componentData, $componentDataEn);
            } else {
                $componentData = $_POST[$componentFieldName];
                if($componentId == 6){$componentData = password_hash($componentData, PASSWORD_BCRYPT);
                }
                $component->updateComponentData($componentName, $instance,$componentData, null);
            }
            header("location: ../../index.php");
        }
    }else if($action == "deleteData"){
        $instance = $_SESSION['id'];
        $components = $component->getModuleComponents();
        foreach ($components as $c) {
            $componentName = $c['name'];
            $component->deleteComponentData($instance);
        }
        header("location: ../../index.php");

    }

}