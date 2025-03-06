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
        if($tableName != null){
            $this->tableName = $tableName;
        }else{
            if($this->getTableViaName()['success']){
                $this->tableName = $this->getTableViaName()['data'];
                echo $this->tableName;
            }
        }
        $this->moduleId = $this->getID();
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
    private function getTableViaName(): array
    {
        $result = [
            'success' => false,
            'data' => null,
            'error' => null,
        ];
        try {
            //find module table name from table modules
            $sql = "SELECT table FROM `modules` WHERE module_name = :moduleName";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':moduleName' => $this->moduleName]);
            $moduleTableName = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$moduleTableName || !isset($moduleTableName['module_table'])) {
                $result['error'] = 'Module table not found'; // Error if no row was fetched or column is missing
                return $result; // Early return to avoid further execution
            }
            //compare with db
            $queryCheck = $this->db->prepare("SHOW TABLES LIKE :tableName");
            $queryCheck->execute([':tableName' => $moduleTableName['module_table']]);
            $tableExists = $queryCheck->fetch();
            if ($tableExists) {
                $result['data'] = $moduleTableName['module_table'];
                $result['success'] = true;
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
                $result['error'] = $insertName['error'] . $insertTable['error'];
            }

        }else{
            $result['error'] = $moduleTableIsUnique['error'] . ' ' . $moduleNameIsUnique['error'];

        }
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

    public function deleteModule()
    {
        if ($this->tableName != '') {
            try {
                $sql = "DELETE FROM `modules` WHERE module_name = :moduleName";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':moduleName' => $this->moduleName]);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                echo "Entry with module table cannot be deleted";
                exit();
            }
            try {
                $sql = "DROP TABLE IF EXISTS `$this->tableName`";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                echo "Module table cannot be delete";
                exit();
            }
        } else {
            echo "Module table does not exists";
        }
        return true;
    }

    public function getModuleComponents(){
        $sql = "SELECT * FROM `module_components` WHERE module_id= :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $this->moduleId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function updateModuleTableFields(){
        $components = $this->getModuleComponents();
        foreach($components as $component){
            if($this->componentIncluded($component['name'])) echo $component['name'].' already exists';

        }


    }

    /**
     * @return bool
     * Check if column for this component is already included in current module table
     */
    private function componentIncluded($componentName): bool
    {

        $sql = "SHOW COLUMNS FROM $tableName LIKE $componentName";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if($stmt->fetch()){
            return true;
        }else{
            return false;
        }
    }
}