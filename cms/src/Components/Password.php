<?php

include_once("component.php");
class Password extends component{

    public static function getFields($componentId): string
    {
        return "
        <label for='textField' class='form-label mt-3'>Název komponenty</label>
        <input class='form-control' type='text' id='textField' name='component_name' placeholder='...' required/>
        
        <input type='hidden' id='component_id'  value=".$componentId." name='component_id' >
        <div class='mt-3'>
            <div class='form-check form-switch'>
            <input type='hidden' name='component_isRequired' value='0'>
            <input name='component_isRequired' class='form-check-input' type='checkbox' id='isRequired' checked value='1'/>
            <label class='form-check-label' for='isRequired'>Komponenta je povinná</label>
            </div>
         </div>
         
        <div class='my-3'>
            <div class='form-check form-switch'>
            <input type='hidden' name='component_isMultlang' value='0'>
            <input name='component_isMultlang' class='form-check-input' type='checkbox' id='isMultilang' checked value='1'/>
            <label class='form-check-label' for='isMultilang'>Komponenta je vícejazyčná</label>
            </div>
        </div>
    ";
    }

    public function getDataFieldsForEdit(): string{
        $out = '';
        if($this->componentIsMultlang === 1){


            $out .= "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>".$this->componentName . ' CZ' . "</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='' name='component_" . $this->componentName ."' placeholder='*****' " . ($this->componentIsRequired ? 'required' : '') . "/>";

            $out .= "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>".$this->componentName . ' EN' . "</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='' name='component_en_" . $this->componentName ."' placeholder='*****' " . ($this->componentIsRequired ? 'required' : '') . "/>";
        }else{
            $out = "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>" . $this->componentName . "</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='' name='component_" . $this->componentName ."' placeholder='*****' " . ($this->componentIsRequired ? 'required' : '') . "/>";
        }
        return $out;
    }

    public function getDataFieldsForInsert(): string
    {

        if($this->componentIsMultlang === 1){

            $out = "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>".$this->componentName . ' CZ' . "</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='' name='component_" . $this->componentName ."' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "/>";

            $out .= "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>".$this->componentName . ' EN' . "</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='' name='component_en_" . $this->componentName ."' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "/>";
        }else{
            $out = "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>".$this->componentName ."</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='' name='component_" . $this->componentName ."' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "/>";
        }
        return $out;

    }

//    public function getDataFormated():array{
//        return [
//            password_hash($this->componentData,PASSWORD_BCRYPT),
//            password_hash($this->componentDataEn, PASSWORD_BCRYPT)
//            ];
//    }
    public function getDataFormated():array{
        return [null, null];
    }

}