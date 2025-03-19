<?php


class Password{

    protected string $componentName;
    protected int $componentId;
    protected int $componentIsRequired;
    protected int $componentIsMultlang;
    protected string $componentData = '';
    protected string $componentDataEn = '';

    public function __construct($componentName, $componentId, int $componentIsRequired, int $componentIsMultlang, $componentData = null, $componentDataEN = null) {
        $this->componentName = $componentName;
        $this->componentId = $componentId;
        $this->componentIsRequired = $componentIsRequired;
        $this->componentIsMultlang = $componentIsMultlang;
        if($componentData !== null){
            $this->componentData = $componentData;
        }
        if($componentDataEN !== null){
            $this->componentDataEn = $componentDataEN;
        }
    }

    public static function getFields(): string
    {
        return "
        <label for='textField' class='form-label mt-3'>Název komponenty</label>
        <input class='form-control' type='text' id='textField' name='component_name' placeholder='...' required/>
        
        <input type='hidden' id='component_id' value='6' name='component_id' >
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

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='". $this->componentData. "' name='component_" . $this->componentName ."' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "/>";

            $out .= "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>".$this->componentName . ' EN' . "</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='". $this->componentDataEn . "' name='component_en_" . $this->componentName ."' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "/>";
        }else{
            $out = "<label for='textField_". $this->componentName ."' class='form-label mt-2 mb-1'>" . $this->componentName . "</label>";

            $out .= "<input class='form-control' type='text' id='text". $this->componentName."' value='" . $this->componentData . "' name='component_" . $this->componentName ."' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "/>";
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
}