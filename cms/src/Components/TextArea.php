<?php
include_once("component.php");
class TextArea extends component
{
    public function getDataFieldsForEdit(): string
    {
        $out = '';
        if ($this->componentIsMultlang === 1) {
            $out .= "<label for='textArea_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . " CZ</label>";
            $out .= "<textarea class='form-control' id='textArea_" . $this->componentName . "' name='component_" . $this->componentName . "' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . ">" . htmlspecialchars($this->componentData) . "</textarea>";

            $out .= "<label for='textArea_" . $this->componentName . "_en' class='form-label mt-2 mb-1'>" . $this->componentName . " EN</label>";
            $out .= "<textarea class='form-control' id='textArea_" . $this->componentName . "_en' name='component_en_" . $this->componentName . "' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . ">" . htmlspecialchars($this->componentDataEn) . "</textarea>";
        } else {
            $out .= "<label for='textArea_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . "</label>";
            $out .= "<textarea class='form-control' id='textArea_" . $this->componentName . "' name='component_" . $this->componentName . "' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . ">" . htmlspecialchars($this->componentData) . "</textarea>";
        }
        return $out;
    }

    public function getDataFieldsForInsert(): string
    {
        $out = '';
        if ($this->componentIsMultlang === 1) {
            $out .= "<label for='textArea_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . " CZ</label>";
            $out .= "<textarea class='form-control' id='textArea_" . $this->componentName . "' name='component_" . $this->componentName . "' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "></textarea>";

            $out .= "<label for='textArea_" . $this->componentName . "_en' class='form-label mt-2 mb-1'>" . $this->componentName . " EN</label>";
            $out .= "<textarea class='form-control' id='textArea_" . $this->componentName . "_en' name='component_en_" . $this->componentName . "' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "></textarea>";
        } else {
            $out .= "<label for='textArea_" . $this->componentName . "' class='form-label mt-2 mb-1'>" . $this->componentName . "</label>";
            $out .= "<textarea class='form-control' id='textArea_" . $this->componentName . "' name='component_" . $this->componentName . "' placeholder='...' " . ($this->componentIsRequired ? 'required' : '') . "></textarea>";
        }
        return $out;
    }
}
