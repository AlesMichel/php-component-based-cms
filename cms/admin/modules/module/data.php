<?php
include_once("../../../autoload.php");
$out = '';

$moduleName = $_GET["module_name"];
$module = new Module($moduleName);
$_SESSION["module_name"] = $moduleName;
$out .= cmsDefaultPage::buildNavTabs($moduleName);
$moduleData = $module->getModuleDataForAdmin();
$moduleComponents = $module->getModuleComponents();

if (empty($moduleComponents)) {
    $out .= "<p>No components found for this module.</p>";
} elseif (empty($moduleData)) {
    $out .= "<p>Tento modul nema zadne záznamy</p>";

    $out .= "<form method='POST' action='entry.php' class=''>";
    $out .= "<button class='btn btn-primary btn-sm my-3' name='method' value='add' type='submit'>Přidat nový záznam</button>";
    $out .= "</form>";
} else {
    $out .= "<div class=''><h5>Záznamy pro modul: " . htmlspecialchars($moduleName) . "</h5></div>";
    $out .= "<form method='POST' action='entry.php' class=''>";
    $out .= "<button name='method' class='btn btn-primary btn-sm my-3' value='add' type='submit'>Přidat nový záznam</button>";
    $out .= "</form>";

    foreach ($moduleData as $moduleId => $components) {

        $out .= "<h6>Instance ID: " . htmlspecialchars($moduleId) . "</h6>";
        $out .= "<table class='table table-bordered'>";
        $out .= "<thead>
                 <tr>
                    <th>Název komponenty</th>
                    <th>Hodnota komponenty</th>
                    <th>Akce</th>
                 </tr>
             </thead>";
        $out .= "<tbody>";

        foreach ($components as $component) {
            $out .= "<tr>";
            $out .= "<td>" . htmlspecialchars($component['componentname']) . "</td>";
            $out .= "<td>" . htmlspecialchars($component['componentvalue']) . "</td>";
            $out .= "<td>
            <form method='POST' action='entry.php'>
                <input name='id' type='hidden' value=" . $component['componentid'] . ">
                <button class='btn btn-sm btn-primary' name='method' value='edit'>Upravit</button>
            </form>
            </td>";
            $out .= "</tr>";


        }

        $out .= "</tbody>";
        $out .= "</table>";
    }
}

$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();

