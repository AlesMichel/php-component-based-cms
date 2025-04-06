<?php
require_once(__DIR__."/../../src/Database/connect.php");

class Module
{
    protected string $moduleName;
    protected string $tableName;
    protected int $moduleId;
    protected PDO $db;


    public function __construct($moduleName, $tableName = null)
    {
        $this->db = connect::getInstance()->getConnection();
        $this->moduleName = $moduleName;
        if ($tableName) {
            $this->tableName = $tableName;
        } else {
            $this->tableName = $this->getTableViaName()['data'];
            $this->moduleId = $this->getIDViaName()['data'];
        }
    }

    #region getters
    public function getTableName()
    {
        return $this->tableName;
    }
    public function getID(): int
    {
        return $this->getIDViaName()['data'];

    }
    public function getName(): string
    {
        return $this->moduleName;
    }
    public function getTableViaName(): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'error' => null,
        ];
        try {
            //find module table name from table modules
            $sql = "SELECT module_table FROM `modules` WHERE module_name = :moduleName";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':moduleName' => $this->moduleName]);
            $moduleTableName = $stmt->fetch(PDO::FETCH_COLUMN);
            if($moduleTableName === false){
                $result['success'] = false;
                $result['error'] = "module table not found";
            }else{

                $queryCheck = $this->db->prepare("SHOW TABLES LIKE ?");
                $queryCheck->execute([$moduleTableName]);
                $tableExists = $queryCheck->fetch(PDO::FETCH_COLUMN);
                if ($tableExists) {
                    $result['data'] = $moduleTableName;
                    $result['success'] = true;
                }
            }
        } catch (PDOException $e) {
            $result['error'] = "Error fetching table name: " . $e->getMessage();
        }
        return $result;
    }

    protected function getIDViaName(): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'error' => null,
        ];
        try {
            $sql = "SELECT id FROM `modules` WHERE module_name = :moduleName";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':moduleName' => $this->moduleName]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $result['data'] = $row['id'];
                $result['success'] = true;
            }else{
                $result['error'] = 'Module Not Found';
            }
        } catch (PDOException $e) {
            $result['error'] = "Error fetching module data: " . $e->getMessage();
        }
        return $result;
    }
    private function getNameViaId(): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'error' => null,
        ];
        try {
            $sql = "SELECT module_name FROM `modules` WHERE id= :id ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $this->moduleId]);
            $getName = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($getName) {
                $result['data'] = $getName['module_name'];
                $result['success'] = true;
            } else {
                $result['error'] = "Module name not found";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        return $result;
    }

    /**
     * @return int
     * get highest id in current module table
     */
    public function getHighestInstance(): int {
        try {
            $stmt = $this->db->prepare("SELECT MAX(`id`) AS max_id FROM `$this->tableName`");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['max_id'] ?? 0); // vrátí 0, pokud tabulka je prázdná
        } catch (Exception $e) {
            echo "Chyba: " . $e->getMessage();
            return 0;
        }
    }

    #endregion getters

    //processes

    public function createNewModule():array{
        $result = [
            'success' => false,
            'error' => null,
        ];

        $moduleTableIsUnique = $this->tableNameIsUnique();
        $moduleNameIsUnique = $this->moduleNameIsUnique();

        if($moduleTableIsUnique['success'] === true && $moduleNameIsUnique['success'] === true){

            $insertName = $this->addModuleToCommonTable();
            $insertTable = $this->addModuleToDB();

            if($insertTable['success'] === true && $insertName['success'] === true){
                $result['success'] = true;
            }else{
                $result['error'] = $insertName['error'] . $insertTable['error'];}
            }else{
                $result['error'] = $moduleTableIsUnique['error'] . ' ' . $moduleNameIsUnique['error'];}
        return $result;
    }

    /**
     * @return array
     * Check if table name is not being already in use
     */
    private function moduleNameIsUnique(): array{
        $result = [
            'success' => false,
            'error' => null,
        ];
        try{
            $queryCheck = $this->db->prepare("SELECT * FROM `modules` WHERE `module_name` = :name");
            $queryCheck->bindParam(":name", $this->moduleName);
            $queryCheck->execute();
            $check = $queryCheck->fetchColumn();
            if($check) {
                $result['error'] = 'Module with this name already exists';
            }else{
                $result['success'] = true;
            }
        }catch (PDOException $e) {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @return array
     * Check if module name is not already in use
     */
    private function tableNameIsUnique(): array{

        $result = [
            'success' => false,
            'error' => null,
        ];

        try {
            $queryCheck = $this->db->prepare("SHOW TABLES LIKE :tableName");
            $queryCheck->execute([':tableName' => $this->tableName]);
            $check= $queryCheck->fetch(PDO::FETCH_ASSOC);
            if ($check) {
                $result['error'] = "Module with this table name already exists.";
            }else{
                $result['success'] = true;
            }
        }catch (PDOException $e) {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    private function addModuleToCommonTable(): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'error' => null,
        ];
        try {

            //module name does not exist, so we proceed to inserting module name into database
            $sql = "INSERT INTO `modules` (module_name, module_table) VALUES (:moduleName, :moduleTableName)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':moduleName' => $this->moduleName, ':moduleTableName' => $this->tableName]);



        } catch (PDOException $e) {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }
    private function addModuleToDB(): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'error' => null,
        ];
        //first check if the table is not already in the database
        try {

            //table does not exist, proceed to creating new table
            //create id, columns for components
            $sql = "CREATE TABLE IF NOT EXISTS `$this->tableName` (
                    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY
                )";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result['success'] = true;
            $result['error'] = '';

        } catch (PDOException $e) {
            $result['error'] = $e->getMessage();
        }
        return $result;
    }

    public function deleteModule(){return $this->deleteModuleTable() && $this->deleteFromModules();}

    private function deleteFromModules(){
        try {
        $sql = "DELETE FROM `modules` WHERE module_name = :moduleName";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':moduleName' => $this->moduleName]);
        } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo "Module table from modules cannot be delete";
        return false;
        }
    }

    private function deleteModuleTable(){
        try {
        $sql = "DROP TABLE IF EXISTS `$this->tableName`";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
        } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        echo "Module table cannot be delete";
        return false;
        }
    }

    public function getModuleComponents(): false|array
    {
        $sql = "SELECT * FROM `module_components` WHERE module_id= :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $this->moduleId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                $name = $component['name'];
                $value = $row[$name] ?? null;
                $componentId = $component['component_id'];
                $instance = $component['id'];

                $newArray[$id][] = [
                    "componentname" => $name,
                    "componentvalue" => $value,
                    "componentid" => $componentId,
                    'instance' => $instance
                ];
            }
        }
        return $newArray;
    }

    public function getModuleData(){
        $sql = "SELECT * FROM `$this->tableName`";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}