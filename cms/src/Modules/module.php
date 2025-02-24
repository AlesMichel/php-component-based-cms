<?php


require_once(__DIR__."/../../src/Database/connect.php");



class Module
{
    protected string $moduleName;
    protected string $tableName;
    protected int $moduleId;
    protected PDO $db;

    public function __construct($moduleName = null, $tableName = null, $moduleId = null)
    {
        //get db conn
        $this->db = connect::getInstance()->getConnection();
        //get module name
        switch (true) {

            // Case: Only moduleId is provided
            case ($moduleName === null && $tableName === null && $moduleId !== null):
                $this->moduleId = $moduleId;
                // Fetch module name and table name based on ID
                $status = $this->getNameViaId();
                if ($status['success']) {
                    $this->moduleName = $status['data'];
                    $tableStatus = $this->getTableViaName();
                    if ($tableStatus['success']) {
                        $this->tableName = $tableStatus['data'];
                    } else {
                        echo $tableStatus['error'];
                    }
                } else {
                    echo $status['error'];
                }
                break;

            // Case: Module name is provided (with or without table name)
            case ($moduleName !== null && $tableName === null && $moduleId === null):
                $this->moduleName = $moduleName;
                $status = $this->getIDViaName();
                if ($status['success']) {
                    $this->moduleId = $status['data'];
                }else{
                    echo $status['error'];
                }
                $status = $this->getTableViaName();
                if ($status['success']) {
                    $this->tableName = $status['data'];
                } else {
                    echo $status['error'];
                }
                break;

            case($moduleName !== null && $tableName !== null):

                $this->moduleName = $moduleName;
                $this->tableName = $tableName;

                //insert table

                $res = $this->createNewModule();
                if($res['success'] === true){
                    $_SESSION['cms_message'] = 'New module has been created';
                    $idRes = $this->getIDViaName();

                    if($idRes['success'] === true){
                        $this->moduleId = $idRes['data'];
                    }
                }else{
                    $_SESSION['cms_message_error'] = $res['error'];
                }

                break;

            // Case: Neither moduleId nor moduleName is provided
            default:
//                echo "Module name or ID must be provided.";
                break;
        }
    }

    #region getters
    public function getTableName()
    {
        return $this->tableName;
    }
    public function getID()
    {
        return $this->moduleId;
    }
    public function getName(){
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
            $sql = "SELECT module_table FROM `modules` WHERE module_name = :moduleName";
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

    private function getIDViaName(): array
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

    private function createNewModule():array{
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
            $check =$queryCheck->fetch(PDO::FETCH_ASSOC);
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
                echo $this->moduleName;
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
            $result['success'] = true;

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
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                module_id INT(11) UNSIGNED,
                component_id INT(11) UNSIGNED,
                component_instance INT(11),
                component_name VARCHAR(255),
                component_data LONGBLOB,
                component_data_en LONGBLOB,
                component_multlang TINYINT(1),
                component_required TINYINT(1),
                FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
                ) ENGINE=INNODB;";
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



}