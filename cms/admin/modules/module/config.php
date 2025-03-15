<?php
include_once("../../../autoload.php");
$out = '';

$moduleName = $_GET["module_name"];
$module = new Module($moduleName);
$_SESSION["module_name"] = $moduleName;

$out .= cmsDefaultPage::buildNavTabs($moduleName);

$out .= '<form action="../process.php" method="post" enctype="multipart/form-data">
         <input type="hidden" name="action" value="delete">
         <button type="submit" class="btn btn-danger" name="module_name" value="'.$moduleName.'" >Smazat modul</button>
         </form>';

$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();


