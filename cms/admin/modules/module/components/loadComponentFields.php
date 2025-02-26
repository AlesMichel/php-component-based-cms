<?php

include_once("../../../../autoload.php");

$db = connect::getInstance()->getConnection();

// loadComponentFields.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['component_id'])) {

        $componentId = $_POST['component_id'];
        $_SESSION['component_id'] = $componentId;
        $currentModule = $_SESSION['current_module_id'];
        echo '<form method="POST" action="process.php">';
        echo componentCommon::createComponent($componentId);
        echo '<button class="btn btn-primary mt-1" type="submit">Vytvořit</button>';
        echo '<input type="hidden" name="action" value="create">';
        echo '</form>';

}}
