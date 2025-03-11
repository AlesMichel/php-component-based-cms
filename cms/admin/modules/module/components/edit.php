<?php
include("../../../../autoload.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $componentName = $_POST['componentName'];
    $moduleName = $_SESSION['moduleName'];
    $component = new componentCommon($moduleName);
    $componentParams = $component->getComponentParams($componentName);
    $out = '';

    $out .= "<form method='POST' action='process.php'>
        <div class='mb-3'>
        <label for='component_name' class='form-label'>Nazev komponenty</label>
        <input type='text' class='form-control' id='component_name' name='component_name' value=".$componentParams['name']." readonly>
        </div>
        <div class='mb-3'>
        <label for='component_id' class='form-label'>Component ID</label>
        <input type='text' class='form-control' id='component_id' name='component_id' value=".$componentParams['component_id']." readonly>
        </div>

        <div class='mb-3'>
        <label for='module_id' class='form-label'>Module ID</label>
        <input type='text' class='form-control' id='module_id' name='module_id' value=".$componentParams['module_id']." readonly>
        </div>
        
        <div class='mt-3'>
            <div class='form-check form-switch'>
            <input type='hidden' name='component_isRequired' value='0'>
            <input name='component_isRequired' class='form-check-input' type='checkbox' id='isRequired' value='1'  " . ($componentParams['required'] == 1 ? 'checked' : '') . " />
            <label class='form-check-label' for='isRequired'>Komponenta je povinná</label>
            </div>
         </div>
         
        <div class='my-3'>
            <div class='form-check form-switch'>
            <input type='hidden' name='component_isMultilang' value='0'>
            <input name='component_isMultlang' class='form-check-input' type='checkbox' id='isMultilang' value='1'  " . ($componentParams['multilang'] == 1 ? 'checked' : '') . " />
            <label class='form-check-label' for='isMultilang'>Komponenta je vícejazyčná</label>
            </div>
       </div>
            
  

        <button name='action' value='update' type='submit' class='btn btn-primary'>Upravit</button>
        <button name='action' value='delete' type='submit' class='btn btn-danger ms-2'>Smazat</button>
        </form>";

    $buildPage = new cmsDefaultPage($out);
    $buildPage->buildLayout();

}else{
    echo "No components in current module";
}
