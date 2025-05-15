<?php
include_once("./autoload.php");
$db = connect::getInstance()->getConnection();
$module = new componentCommon("test");

$getData = $module->getModuleDataForAdminObjects();
echo "<pre>";
print_r($getData);
echo "</pre>";
