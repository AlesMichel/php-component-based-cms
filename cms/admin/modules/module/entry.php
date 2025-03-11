<?php
include_once("../../../autoload.php");

$out = '';

//1. decide if creating new entries or editing
if($_SERVER ['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['method'];

    if($action == 'add'){
        $moduleName = $_SESSION['module_name'];
        $components = new componentCommon($moduleName);
        //get fields
        $out .= '<form action="components/process.php" method="post" enctype="multipart/form-data" >';
        $out .= $components->getInsertFields();
        $out .= '<button class="btn btn-primary mt-3" type="submit" name="action" value="insert">Pridat</button>';
        $out .= "</form>";


    }
}





$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();
