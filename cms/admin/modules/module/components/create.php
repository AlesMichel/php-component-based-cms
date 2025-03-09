<?php

include_once("../../../../autoload.php");


echo '<script src="handleDynamicForms.js"></script>';

$db = connect::getInstance()->getConnection();

if($_SERVER["REQUEST_METHOD"] == "POST") {
//    $currentModule = $_GET['current_module']; // Get current module
//    $componentId = $_POST['component_id'];  // Get the selected component's ID
    //now we have components id and we wil fetch its name by id
    
    $out = '';

    $out .= componentCommon::renderComponents($db);

    $buildPage = new cmsDefaultPage($out);
    $buildPage->buildLayout();


}