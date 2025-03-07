<?php



include_once(__DIR__ . "/../Modules/module.php");
require_once("TextField.php");
require_once("TextArea.php");
require_once("Position.php");
require_once("Image.php");


class componentCommon extends module{

    public function __construct($name) {
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
        if($unique){
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
        }else{
            $result['success'] = false;
        }
        //add column in module table
        return $result;
    }
    private function componentNameIsUnique($componentName): array
    {
        $moduleId = $this->getID();
        $result = [
            'success' => false,
            'error' => null,
        ];
        try{
            $queryCheck = $this->db->prepare("SELECT * FROM `module_components` WHERE `name` = :name AND `module_id` = :module_id");
            $queryCheck->bindParam(":name", $componentName);

            $queryCheck->bindParam(":module_id", $moduleId);
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



    public static function fetchAllComponents($db) {
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
    public static function renderComponents($db): string {
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
        if($componentId === null) {
            echo "No component found";
        }else if($componentId == 1) {
            $out .= TextField::getFields();
        }elseif($componentId == 2) {
            $out .= Image::getFields();
        } elseif($componentId == 3) {
            $out .= Position::getFields();
        }elseif($componentId == 4) {
            $out .= TextArea::getFields();
        }
        return $out;
    }
    public static function printComponentTable($componentId, $componentName, $db):string{
        $componentType = self::findComponentTypeById($componentId, $db);
        return '<table class="table table-bordered">
                <tr>
                    <td>Název komponenty</td>
                    <td>'. $componentName .'</td>
                </tr>
                <tr>
                    <td>Typ komponenty</td>
                    <td>'. $componentType .'</td>
                </tr>

            </table>';
    }
    public static function findComponentTypeById($componentId, $db){
        try{
            $sql = 'SELECT component_type FROM components WHERE id = :component_id';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':component_id', $componentId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $componentType = $row["component_type"];
            if($componentType){
                return $componentType;
            }else{
                return false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    public function getComponentFields($insertArray, $edit = false): string
    {
        $out = '';
        if($edit){
            foreach($insertArray as $component){
                $getComponentId = $component['component_id'];
                $getComponentName = $component['component_name'];
                $getComponentIsRequired = (int)$component['component_required'];
                $getComponentIsMultlang = (int)$component['component_multlang'];
                $getComponentData = $component['component_data'];
                $getComponentDataEn = $component['component_data_en'];

                $_SESSION["component_pass_data_update"][] = [
                    'component_id' => $getComponentId,
                    'component_name' => $getComponentName,
                    'component_required' => $getComponentIsRequired,
                    'component_multlang' => $getComponentIsMultlang
                ];

                if($getComponentId == 1){
                    $textField = new TextField($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang, $getComponentData, $getComponentDataEn);
                    $out .= $textField->getDataFieldsForEdit();
                }else if($getComponentId == 2){

                    $image = new Image($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang, $getComponentData, $getComponentDataEn);
                    $out .= $image->getDataFieldsForEdit();

                }else if($getComponentId == 3){
                    $position = new Position($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang, $getComponentData, $getComponentDataEn);
                    $out .= $position->getDataFieldsForEdit();
                }else if($getComponentId == 4){
                    $textArea = new TextArea($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang, $getComponentData, $getComponentDataEn);
                    $out .= $textArea->getDataFieldsForEdit();
                }
                else{
                    $out .= 'No data fields found';
                }
            }
        }
        else{
            foreach($insertArray as $component){
                $getComponentName = $component['component_name'];
                $getComponentId = $component['component_id'];
                $getComponentIsRequired = $component['component_required'];
                $getComponentIsMultlang = $component['component_multlang'];

                if($getComponentId == 1){
                    $textField = new TextField($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                    $out .= $textField->getDataFieldsForInsert();
                }else if($getComponentId == 2){
                    $image = new Image($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                    $out .= $image->getDataFieldsForInsert();
                }else if($getComponentId == 3){
                    $position = new Position($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                    $out .= $position->getDataFieldsForInsert();
                }else if($getComponentId == 4){
                    $textArea = new TextArea($getComponentName, $getComponentId, $getComponentIsRequired, $getComponentIsMultlang);
                    $out .= $textArea->getDataFieldsForInsert();
                }

                else{
                    $out .= 'No data fields found';
                }
            }
        }
        return $out;
    }
}
