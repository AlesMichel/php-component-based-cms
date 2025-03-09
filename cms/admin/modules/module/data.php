<?php
include_once("../../../autoload.php");
$out = '';

// Print navigation
$moduleName = $_GET["module_name"];
$_SESSION['module_name'] = $moduleName;
$module = new Module($moduleName);

$out .= cmsDefaultPage::buildNavTabs($moduleName);

// Get module data for admin
$moduleData = $module->getModuleDataForAdmin();
var_dump($moduleData); // Debugging to check the structure of $moduleData

// Check if any components are found for this module
if (empty($moduleData)) {
    $out .= "<p>No components found for this module.</p>";
} elseif (empty($moduleData)) {
    $out .= "<p>Tento modul nema zadne záznamy</p>";

    $out .= "<form method='POST' action='newEntry.php' class=''>";
    $out .= "<button class='btn btn-primary btn-sm my-3' type='submit'>Přidat nový záznam</button>";
    $out .= "</form>";
} else {
    $out .= "<div class=''><h5>Záznamy pro modul: " . htmlspecialchars($moduleName) . "</h5></div>";
    $out .= "<form method='POST' action='entry.php' class=''>";
    $out .= "<button name='method' class='btn btn-primary btn-sm my-3' value='add' type='submit'>Přidat nový záznam</button>";
    $out .= "</form>";

    // Loop through each module data entry
    foreach ($moduleData as $moduleId => $components) {
        // Create a new section for each module ID
        $out .= "<h6>Instance ID: " . htmlspecialchars($moduleId) . "</h6>";
        $out .= "<table class='table table-bordered'>";
        $out .= "<thead>
                 <tr>
                    <th>Název komponenty</th>
                    <th>Hodnota komponenty</th>
                 </tr>
             </thead>";
        $out .= "<tbody>";

        // Loop through the components of each module
        foreach ($components as $component) {
            // Start the table row
            $out .= "<tr>";
            $out .= "<td>" . htmlspecialchars($component['componentname']) . "</td>";
            $out .= "<td>" . htmlspecialchars($component['componentvalue']) . "</td>";
            $out .= "</tr>";
        }

        $out .= "</tbody>";
        $out .= "</table>";
    }
}

$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();
?>
