<?php

include_once(__DIR__ . "/../Modules/module.php");
include_once("TextField.php");
include_once("TextArea.php");
include_once("Position.php");
include_once("Image.php");
include_once("Date.php");
include_once("Password.php");
include_once("Time.php");
include_once("SelectBox.php");

class componentCommon extends module
{
    public function __construct($name)
    {
        parent::__construct($name, null);
    }

    public function getLastInsertId(): int {
        $lastInsertId = (int) $this->db->lastInsertId();
        return $lastInsertId > 0 ? $lastInsertId : 1;
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
                if($stmt->execute([
                    ':module_id' => $this->getID(),
                    ':component_id' => $componentId,
                    ':name' => $componentName,
                    ':multilang' => $componentIsMultilang,
                    ':required' => $componentIsRequired
                ])){
                    $this->updateModuleTableFields();
                }
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

    public function insertOption($componentName, string $optionValue, $optionValueEn = null): bool
    {
        if ($optionValueEn === null) {
            $optionValueEn = '';
        }

        try {
            // SQL dotaz pro vložení hodnoty do tabulky 'component_resource'
            $sql = "INSERT INTO component_resource (module_id, component_name, value, valueEn) 
                VALUES (:moduleId, :componentName, :componentValue, :optionValueEn)";

            // Příprava dotazu
            $stmt = $this->db->prepare($sql);

            // Exekuujeme dotaz s hodnotami z funkce
            $stmt->execute([
                "moduleId" => $this->moduleId,
                "componentName" => $componentName,
                "componentValue" => $optionValue,
                "optionValueEn" => $optionValueEn
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
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

    public static function createComponent(int $componentId): string
    {
        $path = self::buildPath($componentId);
        return $path::getFields($componentId);

    }

    public static function buildPath(int $id){
        $sql = "SELECT name FROM components WHERE id = :id";
        $db = connect::getInstance()->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn();
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
                $path = self::buildPath($getComponentId);
                $component = new $path($this->moduleName,$getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                $out .= $component->getDataFieldsForInsert();
        }
        return $out;
    }

    public function getModuleComponentsObjects():array{
        $components = [];
        foreach ($this->getModuleComponents() as $component) {
            $getComponentId = $component['component_id'];
            $getComponentName = $component['name'];
            $getComponentIsMultlang = $component['multilang'];
            $getComponentIsRequired = $component['required'];
            $path = self::buildPath($getComponentId);
            $componentObject = new $path($this->moduleName,$getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
            $components[] = $componentObject;
        }
        return $components;

    }

    public function getModuleDataForAdminObjects(): array
    {
        $moduleComponents = $this->getModuleComponents();
        $moduleData = $this->getModuleData();

        $newArray = [];

        // Loop through the module data
        foreach ($moduleData as $row) {
            $id = $row['id']; // Get the ID from the module data
            if (!isset($newArray[$id])) {
                // Initialize the entry for this ID if it doesn't exist yet
                $newArray[$id] = [];
            }

            // Loop through the module components
            foreach ($moduleComponents as $component) {
                $getComponentId = $component['component_id'];
                $getComponentName = $component['name'];
                $getComponentIsMultlang = $component['multilang'];
                $getComponentIsRequired = $component['required'];
                $getData = $row[$getComponentName] ?? null;
                $getDataEn = null;
                if ($getComponentIsMultlang == 1) {
                    $enFieldName = $getComponentName . 'EN';
                    $getDataEn = $row[$enFieldName] ?? null;
                }
                $path = self::buildPath($getComponentId);
                // Create the component object
                $componentObject = new $path(
                    $this->moduleName,
                    $getComponentName,
                    $getComponentId,
                    $getComponentIsRequired,
                    $getComponentIsMultlang,
                    $getData,
                    $getDataEn
                );
                $newArray[$id][] = ['componentObject' => $componentObject] ;
            }
        }

        return $newArray;
    }

    public function getModuleDataForAdminForInstance($instance): array
    {
        $moduleComponents = $this->getModuleComponents();
        $moduleData = $this->getModuleData();
        $newArray = [];

        foreach ($moduleData as $row) {
            $id = $row['id'];

            if ((int)$id !== (int)$instance) {
                continue;
            }

            if (!isset($newArray[$id])) {
                $newArray[$id] = [];
            }

            foreach ($moduleComponents as $component) {
                $name = $component['name'] ?? ''; // Výchozí hodnota
                $data = $row[$name] ?? null;
                $componentId = $component['component_id'] ?? null;
                $multilang = $component["multilang"] ?? 0;
                $required = $component["required"] ?? 0;
                $dataEn = null;

                if ($multilang == 1) {
                    $enFieldName = $name . 'EN';
                    $dataEn = $row[$enFieldName] ?? null;
                }

                $componentData = [
                    "name" => $name,
                    "data" => $data,
                    "dataEn" => $dataEn,
                    "id" => $componentId,
                    "multilang" => $multilang,
                    "required" => $required
                ];

                $newArray[$id][] = $componentData;
            }
        }

        return $newArray;
    }

    public function getModuleDataForAdminForInstanceObjects($instance): array
    {
        $moduleComponents = $this->getModuleComponents();
        $moduleData = $this->getModuleData();
        $newArray = [];

        foreach ($moduleData as $row) {
            $id = $row['id'];

            if ((int)$id !== (int)$instance) {
                continue;
            }

            if (!isset($newArray[$id])) {
                $newArray[$id] = [];
            }

            foreach ($moduleComponents as $component) {
                $name = $component['name'] ?? ''; // Default value
                $data = $row[$name] ?? null;
                $componentId = $component['component_id'] ?? null;
                $multilang = $component["multilang"] ?? 0;
                $required = $component["required"] ?? 0;
                $dataEn = null;

                if ($multilang == 1) {
                    $enFieldName = $name . 'EN';
                    $dataEn = $row[$enFieldName] ?? null;
                }

                // Create the component object instead of just the data array
                $path = self::buildPath($componentId);
                $componentObject = new $path(
                    $this->moduleName,
                    $name,
                    $componentId,
                    $required,
                    $multilang,
                    $data,
                    $dataEn
                );

                // Store the component object in the array
                $newArray[$id][] = [
                    "componentObject" => $componentObject, // Store the object
                    "id" => $componentId,
                    "multilang" => $multilang,
                    "required" => $required
                ];
            }
        }

        return $newArray;
    }


    public function getEditFields($instance): string
    {
        $out = '';
        $data = $this->getModuleDataForAdminForInstance($instance);
        foreach ($data as $row) {
            foreach ($row as $component) {
                $getComponentId = $component['id'];
                $getComponentName = $component['name'];
                $getComponentIsMultlang = $component['multilang'];
                $getComponentIsRequired = $component['required'];
                $getComponentData = $component['data'];
                $getComponentDataEn = $component['dataEn'];
                $path = self::buildPath($getComponentId);
                $component = new $path($this->moduleName,$getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang, $getComponentData, $getComponentDataEn);
                $out .= $component->getDataFieldsForEdit();
            }
        }
        return $out;
    }
    /**
     * @return array
     * Generated by ChatGPT
     * joins module components and module data for admin
     */
    public function getModuleDataForAdmin(): array
    {
        $moduleComponents = $this->getModuleComponents();
        $moduleData = $this->getModuleData();

        $newArray = [];

        // Loop through the module data
        foreach ($moduleData as $row) {
            $id = $row['id']; // Get the ID from the module data
            if (!isset($newArray[$id])) {
                // Initialize the entry for this ID if it doesn't exist yet
                $newArray[$id] = [];
            }
            // Loop through the module components
            foreach ($moduleComponents as $component) {
                $getComponentId = $component['component_id'];
                $getComponentInstance = $component['id'];
                $getComponentName = $component['name'];
                $getComponentIsMultlang = $component['multilang'];
                $getComponentIsRequired = $component['required'];
                $getData = $row[$getComponentName] ?? null;
                $getDataEn = null;
                if ($getComponentIsMultlang == 1) {
                    $enFieldName = $getComponentName . 'EN';
                    $getDataEn = $row[$enFieldName] ?? null;
                }

                $getInstance = $component['id'];
                $path = self::buildPath($getComponentId);
                $component = new $path($this->moduleName,$getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang, $getData, $getDataEn);
                $componentData = $component->getDataFormated();

                $newArray[$id][] = [
                    "name" => $getComponentName,
                    "data" => $componentData[0],
                    "dataEn" => $componentData[1],
                    "id" => $getComponentId,
                    'instance' => $getInstance
                ];
            }
        }

        return $newArray;
    }

    public function getModuleDataForInstance($instance)
    {
        $sql = "SELECT * FROM $this->tableName WHERE `id` = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $instance]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $deleteFromModuleTable = $this->deleteComponentFromModuleTable($componentName, $isMultilang);
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


    private function deleteComponentFromModuleTable($componentName, $isMultilang): bool
    {
        $success1 = true;
        $success2 = false;

        try {
            $sql1 = "ALTER TABLE `$this->tableName` DROP COLUMN `$componentName`";
            $this->db->exec($sql1);
            if ($isMultilang == 1) {
                $columnNameEn = $componentName . 'EN';
                $sql2 = "ALTER TABLE `$this->tableName` DROP COLUMN `$columnNameEn`";
                $this->db->exec($sql2);
                $success2 = true;
            }
            return $success1 && $success2;

        } catch (Exception $e) {
            echo "Chyba při mazání sloupce z tabulky $this->tableName: " . $e->getMessage();
            return false;
        }
    }

    protected function getColumnNameEn($componentName): string{
        return $componentName . 'EN';
    }
    public function updateComponentData($componentName,$instance,$componentData, $componentDataEn = null):bool{
        $columnNameEn = $this->getColumnNameEn($componentName);
        try{
            $sql = "UPDATE `$this->tableName` 
                    SET `$componentName` = :componentData"
                . ($componentDataEn !== null ? ", `$columnNameEn` = :componentDataEn" : "") . " 
                    WHERE `id` = :instance";
            $stmt = $this->db->prepare($sql);
            $params = [':instance' => $instance, ':componentData' => $componentData];
            if ($componentDataEn !== null) {
                $params[':componentDataEn'] = $componentDataEn;
            }
            return $stmt->execute($params);
        }catch (Exception $e) {
            echo "Chyba: " . $e->getMessage();
            return false;
        }
    }

    public function insertComponentData($componentName, $instance, $componentData, $componentDataEn = null): bool
    {
        $columnNameEn = $this->getColumnNameEn($componentName);
        $hasEnColumn = $this->columnExists($columnNameEn);
        $useMultilang = $componentDataEn !== null && $hasEnColumn;

        try {
            $sql = "INSERT INTO `$this->tableName` (`id`, `$componentName`";
            $sql .= $useMultilang ? ", `$columnNameEn`" : "";
            $sql .= ") VALUES (:instance, :componentData";
            $sql .= $useMultilang ? ", :componentDataEn" : "";
            $sql .= ") ON DUPLICATE KEY UPDATE `$componentName` = :componentData";
            $sql .= $useMultilang ? ", `$columnNameEn` = :componentDataEn" : "";

            $stmt = $this->db->prepare($sql);
            $params = [
                ':instance' => $instance,
                ':componentData' => $componentData
            ];

            if ($useMultilang) {
                $params[':componentDataEn'] = $componentDataEn;
            }

            return $stmt->execute($params);
        } catch (Exception $e) {
            echo "Chyba: " . $e->getMessage();
            return false;
        }
    }

    protected function columnExists(string $column): bool {
        $stmt = $this->db->prepare("SHOW COLUMNS FROM `$this->tableName` LIKE :column");
        $stmt->execute([':column' => $column]);
        return $stmt->fetch() !== false;
    }

    public function deleteComponentData($instance): bool
    {
        try {
            $sql = "DELETE FROM `$this->tableName` WHERE `id` = :instance";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':instance' => $instance]);
        } catch (Exception $e) {
            echo "Chyba: " . $e->getMessage();
            return false;
        }
    }

    public function updateModuleTableFields(): string
    {
        $message = '';
        $components = $this->getModuleComponentsObjects();
        foreach ($components as $component) {
            $componentName = $component->getComponentName();
            $multilang = $component->getComponentIsMultlang();
            $exists = $this->componentIncluded($componentName);
            if (!$exists) {
                try {
                    $columnType = $this->getComponentColumn($component->getComponentId());
                    if($multilang == 1){
                        $sql = "ALTER TABLE `$this->tableName` 
                            ADD COLUMN `$componentName` $columnType, 
                            ADD COLUMN `{$componentName}EN` $columnType";
                    }else{
                        $sql = "ALTER TABLE `$this->tableName` ADD COLUMN `$componentName` $columnType";
                    }
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute();
                    $message .= "Sloupec `$componentName` byl úspěšně přidán jako `$columnType`.<br>";
                } catch (PDOException $e) {
                    $message .= "Chyba při přidávání sloupce `$componentName`: " . $e->getMessage() . "<br>";
                }
            }
        }
        return $message;
    }

    //rozhodit do component
    public function getComponentColumn($componentId) {
        return match ($componentId) {
            1 => 'VARCHAR(255)',
            2 => 'TEXT',
            3 => 'INT(11)',
            'image' => 'LONGBLOB',
            4 => 'VARCHAR(255)',
            5 => 'DATE',
            default => 'VARCHAR(255)',
        };
    }

    /**
     * @return bool
     * Check if column for this component is already included in current module table
     */
    private function componentIncluded($componentName)
    {
        $sql = "SHOW COLUMNS FROM `$this->tableName` LIKE :column";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['column' => $componentName]);
        return (bool) $stmt->fetch();
    }

}
