<?php
include_once("../../../autoload.php");

$out = '';

$moduleName = $_GET["module_name"];
$_SESSION['moduleName'] = $moduleName;
$module = new Module($moduleName, null);
$moduleComponents = $module->getModuleComponents();
$moduleData = $module->getModuleDataForAdmin();
$moduleId = $module->getID();


///  //print navigaton
$out .= cmsDefaultPage::buildNavTabs($moduleName);
$addComponentForm = "<form method='POST' action='components/create.php'>
                     <div class=''>
                        <h5>Komponenty pro modul: " . $moduleName. "</h5>
                     </div>
                     <button class='btn btn-primary btn-sm my-3' type='submit'>Přidat komponentu</button>
                     </form>";

$out .= $addComponentForm;
if ($moduleComponents) {
    //proceed
    //create form
    $out .= "<table class='table table-bordered'>";
    $out .= "<thead>
        <tr>
            <th>Název komponenty</th>
            <th>Akce</th>
        </tr>
      </thead>";
    $out .= "<tbody>";

    // Loop through each component instance and display the components
        foreach ($moduleComponents as $component) {
            $out .= "<tr>";
            $out .= "<td>" . htmlspecialchars($component['name']) . "</td>";


            // Form with hidden fields to pass component data
            $out .= "<td class='d-flex'>";


            $out .= "<form method='POST' action='components/edit.php'>";
            $out .= "<input type='hidden' name='componentName' value='" . htmlspecialchars($component['name']) . "'>";
            $out .= "<button class='btn btn-primary btn-sm me-3' type='submit'>Upravit</button>";
            $out .= "</form>";

            $out .= "</td>";
            $out .= "</tr>";

    }
    $out .= "</tbody>";
    $out .= "</table>";
}

$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();