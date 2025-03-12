<?php
include_once("../../../autoload.php");

$out = '';
$moduleName = $_SESSION['module_name'];
$component = new componentCommon($moduleName);

if($_SERVER ['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['method'];
    if($action == 'add'){
        $out .= '<form action="components/process.php" method="post" enctype="multipart/form-data" >';
        $out .= $component->getInsertFields();
        $out .= '<button class="btn btn-primary mt-3" type="submit" name="action" value="insert">Pridat</button>';
        $out .= "</form>";
    }else if ($action == 'edit'){
        $id = $_POST['id'];
//        $out .= '<form action="components/process.php" method="post" enctype="multipart/form-data" >';
//        $out .= $component->getInsertFields();
//        $out .= '<button class="btn btn-primary mt-3" type="submit" name="action" value="edit">Upravit</button>';
//        $out .= '<button class="btn btn-danger mt-3 ms-2" type="submit" name="action" value="delete">Smazat</button>';
//        $out .= "</form>"
        var_dump($component->getModuleDataForInstance($id));

    }

}





$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();
