<?php
include_once("../../../autoload.php");


$out ='';

// Print navigation
$moduleName = $_GET["module_name"];
$out .= cmsDefaultPage::buildNavTabs($moduleName);




$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();