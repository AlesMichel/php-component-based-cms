<?php



include("../../templates/cmsDefaultPage.class.php");
require_once("../../DbConnect/connect.php");
require_once("../../modules/components/ComponentsFetch.php");
require_once("../../modules/components/ComponentsFetch.php");
echo '<script src="handleDynamicForms.js"></script>';

use cms\DbConnect\connect;
use components\ComponentsFetch\ComponentsFetch;

$db = connect::getInstance()->getConnection();

if($_SERVER["REQUEST_METHOD"] == "POST") {
//    $currentModule = $_GET['current_module']; // Get current module
//    $componentId = $_POST['component_id'];  // Get the selected component's ID
    //now we have components id and we wil fetch its name by id

    $currentModuleId = $_SESSION['current_module_id'];
    $out = '';

    $out .= ComponentsFetch::renderComponents($db);

    $buildPage = new cmsDefaultPage($out);
    $buildPage->buildLayout();


}