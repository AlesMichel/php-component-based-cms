<?php
include_once("../../../autoload.php");
$out = '';
$moduleName = $_GET["module_name"];
$module = new componentCommon($moduleName);
$_SESSION["module_name"] = $moduleName;
$out .= cmsDefaultPage::buildNavTabs($moduleName);
$moduleData = $module->getModuleDataForAdminObjects();
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

    foreach ($moduleData as $instance => $dataSet) {
        $out .= "<table class='table table-bordered'>";
        $out .= "<thead>
             <tr>
                <th>Název komponenty</th>
                <th>Hodnota komponenty</th>
                <th>Akce</th>
             </tr>
         </thead>";
        $out .= "<tbody>";

        foreach ($dataSet as $componentArray) {
               $component = $componentArray['componentObject'];
                $out .= "<tr>";
                $out .= "<td>" . $component->getComponentName() . "</td>";
                $out .= "<td>" . $component->getComponentData() . "</td>";
                $out .= "<td>
            <form method='POST' action='entry.php'>
                <input name='id' type='hidden' value='" . htmlspecialchars($instance) . "'>
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

