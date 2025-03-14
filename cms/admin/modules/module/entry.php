<?php
include_once("../../../autoload.php");

$out = '';
$moduleName = $_SESSION['module_name'];
$component = new componentCommon($moduleName);
$_SESSION['module_name'] = $moduleName;

if($_SERVER ['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['method'];
    if($action == 'add'){
        $out .= '<form action="components/process.php" method="post" enctype="multipart/form-data" >';
        $out .= $component->getInsertFields();
        $out .= '<button class="btn btn-primary mt-3" type="submit" name="action" value="insert">Pridat</button>';
        $out .= "</form>";
    }else if ($action == 'edit'){
        $id = $_POST['id'];
        $_SESSION['id'] = $id;
        $out .= '<form action="components/process.php" method="post" enctype="multipart/form-data" >';
        $out .= $component->getEditFields($id);
        $out .= '<button class="btn btn-primary mt-3" type="submit" name="action" value="updateData">Upravit</button>';
        $out .= '<button class="btn btn-danger mt-3 ms-2" type="submit" name="action" value="deleteData">Smazat</button>';
        $out .= "</form>";
    }
}


$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();
