<?php

require_once("./components/Component.php");
require_once("./components/Image.php");
require_once("../dbConnect/connect.php");
require_once("./module.php");
include("../templates/cmsDefaultPage.class.php");

use cms\DbConnect\connect;
use cms\Module\module\module;
use cms\src\Templates\cmsDefaultPage;
use components\Component;
use components\Image\Image;

// This is the index page for modules
$out = '';
$db = connect::getInstance()->getConnection();
$moduleName = $_GET["module_name"];
$_SESSION["module_name"] = $moduleName;
$module = new module($moduleName);
$moduleId = $module->getID();

// Print navigation
$out .= cmsDefaultPage::buildNavTabs($moduleName);

if ($moduleName) {
    // Get module ID by its name
    $moduleComponents = $module->getModuleData();
//    $highestInstance = component::getLastInstance($moduleId, $db);
    $highestInstance = $module->getHighestInstance()['data'];
    // Check if any components are found for this module
    if (empty($moduleComponents)) {
        $out .= "<p>No components found for this module.</p>";
    } elseif ($highestInstance === 0) {
        $out .= "<p>Tento modul nema zadne záznamy</p>";
        $_SESSION['current_module_id'] = $moduleId;
        $out .= "<form method='POST' action='newEntry.php' class=''>";
        $out .= "<button class='btn btn-primary btn-sm my-3' type='submit'>Přidat nový záznam</button>";
        $out .= "</form>";
    } else {


        $out .= "<div class=''><h5>Záznamy pro modul: " . htmlspecialchars($moduleName) . " / id: " . htmlspecialchars($moduleId) . "</h5></div>";

        // Add new data set
        $_SESSION['current_module_id'] = $moduleId;
        $out .= "<form method='POST' action='newEntry.php' class=''>";
        $out .= "<button class='btn btn-primary btn-sm my-3' type='submit'>Přidat nový záznam</button>";
        $out .= "</form>";

        // Loop through each component instance and display the components
        foreach ($moduleComponents as $instance => $components) {

            if ($instance > 0) {
                $out .= "<table class='table table-bordered'>";
                $out .= "<thead>
                     <tr>
                        <th>Název komponenty</th>
                        <th>Hodnota komponenty</th>
                     </tr>
                 </thead>";
                $out .= "<tbody>";

                foreach ($components as $component) {


                    // Store component data in the session
                    $_SESSION['component_pass_data'][] = [
                        'id' => $component['id'],
                        'module_id' => $moduleId,
                        'component_id' => $component['component_id'],
                        'instance' => $component['component_instance'],
                        'component_data' => $component['component_data'],
                        'component_name' => $component['component_name']
                    ];
                    // Start the table row
                    if ($component['component_instance'] > 0) {
                        $out .= "<tr>";

                        if($component['component_id'] == 1) {
                            // Otherwise, display the component name and data as plain text
                            $out .= "<td>" . htmlspecialchars($component['component_name']) . "</td>";
                            $out .= "<td>" . htmlspecialchars($component['component_data']) . "</td>";
                        }

                        else if ($component['component_id'] == 2) {
                            $out .= "<td>" . htmlspecialchars($component['component_name']) . "</td>";
                            $out .= "<td>" . Image::viewImage($component['component_data']) . "</td>"; // Display image
                        }
                        else if ($component['component_id'] == 3) {
                            $out .= "<td>" . htmlspecialchars($component['component_name']) . "</td>";
                            $out .= "<td>" . htmlspecialchars($component['component_data']) . "</td>";
                        }
                        else if ($component['component_id'] == 4) {
                            $out .= "<td>" . htmlspecialchars($component['component_name']) . "</td>";
                            $out .= "<td>" . htmlspecialchars($component['component_data']) . "</td>";
                        }

                        // Close the table row
                        $out .= "</tr>";
                    }
                }
                $out .= "<td colspan='2'>";
                $out .= "<form method='POST' action='editEntry.php'>";
                // Hidden input to send the instance ID
                $out .= "<input type='hidden' name='instance_id' value='" . htmlspecialchars($instance) . "'>";
                $out .= "<button class='btn btn-primary btn-sm' type='submit'>Upravit záznam</button>";
                $out .= "</form>";
                $out .= "</td>";
                $out .= "</tr>";

            }
            $out .= "</tbody>";
            $out .= "</table>";

        }


    }
} else {
    $out .= "Module table does not exist";
}

$buildPage = new cmsDefaultPage($out);
$buildPage->buildLayout();

