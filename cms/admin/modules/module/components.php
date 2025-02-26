<?php
include_once("../../../autoload.php");

$out = '';



$moduleName = $_GET["module_name"];
//print navigaton
$out .= cmsDefaultPage::buildNavTabs($moduleName);
$addComponentForm = "<form method='POST' action='components/create.php'><div class=''>
                     <h5>Komponenty pro modul: " . $moduleName. " / id: " . $moduleId . "</h5></div>
                     <button class='btn btn-primary btn-sm my-3' type='submit'>Přidat komponentu</button></form>";

if ($moduleComponentsStatus['success']) {
    $moduleComponents = $moduleComponentsStatus['data'];
    //proceed
    //create form
    $out .= $addComponentForm;
    $out .= "<table class='table table-bordered'>";
    $out .= "<thead>
        <tr>
            <th>Název komponenty</th>
            <th>Akce</th>
        </tr>
      </thead>";
    $out .= "<tbody>";
    // Loop through each component instance and display the components
    foreach ($moduleComponents as $instance => $components) {
        foreach ($components as $component) {
            $out .= "<tr>";
            $out .= "<td>" . htmlspecialchars($component['component_name']) . "</td>";
            // Store component data in the session
            $_SESSION['component_pass_data'] = [
                'id' => $component['id'],
                'module_id' => $moduleId,
                'component_id' => $component['component_id'],
                'component_instance' => $instance,
                'component_name' => $component['component_name'],
                'component_multlang' => $component['component_multlang'],
                'component_required' => $component['component_required']
            ];

            // Form with hidden fields to pass component data
            $out .= "<td class='d-flex'>";
            $out .= "<form method='POST' action='components/edit.php'>";
            $out .= "<button class='btn btn-primary btn-sm me-3' type='submit'>Upravit</button>";
            $out .= "</form>";

            $out .= "<form method='POST' action='components/delete.php'>";
            $out .= "<button class='btn btn-danger btn-sm' type='submit'>Smazat</button>";
            $out .= "</form>";

            $out .= "</td>";
            $out .= "</tr>";
        }
    }
    $out .= "</tbody>";
    $out .= "</table>";
} else {
    $out .= $addComponentForm;
    $out .= $moduleComponentsStatus['error'];
}

$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();