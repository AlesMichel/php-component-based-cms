<?php

include_once(__DIR__ . "/../Modules/module.php");
include_once("TextField.php");
include_once("TextArea.php");
include_once("Position.php");
include_once("Image.php");

class componentCommon extends module
{

    public function __construct($name)
    {
        parent::__construct($name, null);
    }

    public function initComponent($componentName, $componentId, $componentIsMultilang, $componentIsRequired): array
    {
        $result = [
            'success' => true,
            'data' => null,
            'error' => null,
        ];
        //if component is unique
        $unique = $this->componentNameIsUnique($componentName)["success"];

        if ($unique) {
            //create entry in module_components
            try {
                //find module table name from table modules
                $sql = "INSERT INTO `module_components` (module_id, component_id, name, multilang, required) VALUES (:module_id, :component_id, :name, :multilang, :required)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':module_id' => $this->getID(),
                    ':component_id' => $componentId,
                    ':name' => $componentName,
                    ':multilang' => $componentIsMultilang,
                    ':required' => $componentIsRequired
                ]);
            } catch (PDOException $e) {
                $result['error'] = "Error fetching table name: " . $e->getMessage();
                $result['success'] = false;
            }
        } else {
            $result['success'] = false;
            $result['error'] .= 'Component is not unique';
        }
        //add column in module table
        return $result;
    }

    private function componentNameIsUnique(string $componentName): array
    {
        $moduleId = $this->getID();
        $result = [
            'success' => false,
            'error' => null,
        ];
        try {
            $queryCheck = $this->db->prepare("
            SELECT COUNT(*) FROM `module_components` 
            WHERE `name` = :name AND `module_id` = :module_id
        ");
            $queryCheck->bindParam(":name", $componentName);
            $queryCheck->bindParam(":module_id", $moduleId, PDO::PARAM_INT);
            $queryCheck->execute();
            $count = $queryCheck->fetchColumn();
            echo $count;
            if ($count > 0) {
                $result['error'] = 'Module with this name already exists';
            } else {
                $result['success'] = true;
            }
        } catch (PDOException $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }


    public static function fetchAllComponents($db)
    {
        try {
            $sql = 'SELECT * FROM components';
            $stmt = $db->prepare($sql);
            $stmt->execute(); // Execute the query
            $fetchAllComponents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
        return $fetchAllComponents;
    }

    //renders select box with all components available
    public static function renderComponents($db): string
    {
        // Fetch all components from the database
        $data = self::fetchAllComponents($db);
        if ($data === null) {
            return '<p>No components found.</p>';
        }
        $out = '';
        $out .= '<label for="component">Komponenta</label>
                 <select class="form-select" name="component_id" id="createComponentSelect" onchange="handleDynamicForm()">';
        // Loop through each component and create an option element
        $out .= '<option selected>-</option>';
        foreach ($data as $component) {
            $out .= '<option value="' . htmlspecialchars($component['id']) . '">' .
                htmlspecialchars($component['name']) .
                '</option>';
        }
        $out .= '</select>';
        $out .= '<div id="dynamic-fields"></div>';
        return $out;
    }

    public static function createComponent($componentId): string
    {
        //new out
        $out = '';
        //build field
        if ($componentId === null) {
            echo "No component found";
        } else if ($componentId == 1) {
            $out .= TextField::getFields();
        } elseif ($componentId == 2) {
            $out .= Image::getFields();
        } elseif ($componentId == 3) {
            $out .= Position::getFields();
        } elseif ($componentId == 4) {
            $out .= TextArea::getFields();
        }
        return $out;
    }

    public function getInsertFields(): string
    {
        $out = '';
        $moduleComponents = $this->getModuleComponents();
        foreach ($moduleComponents as $component) {
            $getComponentId = $component['component_id'];
            $getComponentName = $component['name'];
            $getComponentIsMultlang = $component['multilang'];
            $getComponentIsRequired = $component['required'];

            if ($getComponentId == 1) {
                $textField = new TextField($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                $out .= $textField->getDataFieldsForInsert();
            } else if ($getComponentId == 2) {
                $image = new Image($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                $out .= $image->getDataFieldsForInsert();
            } else if ($getComponentId == 3) {
                $position = new Position($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                $out .= $position->getDataFieldsForInsert();
            } else if ($getComponentId == 4) {
                $textArea = new TextArea($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                $out .= $textArea->getDataFieldsForInsert();
            }
        }
        return $out;

    }

    public function getComponentParams($componentName)
    {
        $sql = "SELECT * FROM `module_components` WHERE `name` = :componentName";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['componentName' => $componentName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteComponent($componentName, $isMultilang): bool
    {
        //1. delete component in current table
        $deleteFromModuleTable = $this->deleteComponentFromModuleTable($componentName);
        //2. delete component in common module_components
        $deleteFromModuleComponents = $this->deleteComponentFromModuleComponents($componentName, $isMultilang);
        if ($deleteFromModuleTable and $deleteFromModuleComponents) {
            return true;
        } else {
            return false;
        }
    }

    private function deleteComponentFromModuleComponents($componentName, $isMultilang)
    {
        try {
            $sql = "DELETE FROM module_components WHERE name = :componentName";
            $stmt = $this->db->prepare($sql);

            if ($isMultilang == 1) {
                $columnNameEn = $componentName . 'EN';

                // Smažeme obě varianty (s EN i bez)
                $success1 = $stmt->execute([':componentName' => $columnNameEn]);
                $success2 = $stmt->execute([':componentName' => $componentName]);
                return $success1 && $success2; // Vrátí true pouze pokud obě operace uspějí
            } else {
                return $stmt->execute([':componentName' => $componentName]); // Vrátí výsledek mazání
            }
        } catch (Exception $e) {
            echo "Chyba při mazání z tabulky module_components: " . $e->getMessage();
            return false;
        }
    }


    private function deleteComponentFromModuleTable($componentName): bool
    {
        try {
            $sql = "ALTER TABLE `$this->tableName` DROP COLUMN `$componentName`";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        } catch (Exception $e) {
            echo "Chyba při mazání sloupce z tabulky $this->tableName: " . $e->getMessage();
            return false;
        }
    }

    public function saveComponentData($componentName, $componentData, $componentDataEn, $isMultilang){
        try{
            $sql = "INSERT INTO `$this->tableName` WHERE COLUMN :columnName VALUES (:componentData) ";
            $stmt = $this->db->prepare($sql);
            if ($isMultilang == 1) {
                $columnNameEn = $componentName . 'EN';
                $success1 = $stmt->execute([':componentName' => $componentName, ':componentData' => $componentData]);
                $success2 = $stmt->execute([':componentName' => $columnNameEn, ':componentData' => $componentDataEn]);
                return $success1 && $success2;
            } else {
                return $stmt->execute([':componentName' => $componentName, ':componentData' => $componentDataEn]);
            }
        }catch (Exception $e){

        }
    }


}
