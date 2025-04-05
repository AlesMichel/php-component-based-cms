<?php
include_once("component.php");
class Image extends component
{

    public static function getFields($componentId): string
    {
        return "
        <label for='textField' class='form-label mt-3'>Název komponenty</label>
        <input class='form-control' type='text' id='textField' name='component_name' placeholder='...' required/>
        <input type='hidden' id='component_id' value=".$componentId." name='component_id'>
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

    //ai
    public static function viewImage($filename): string {
        // Create the relative URL for the image
        $src = '/cms/admin/uploads/' . basename($filename);

        // Return the HTML to display the image
        return '<img src="' . htmlspecialchars($src) . '" style="max-width:100px; min-width:100px;" alt="Image" class="img-thumbnail" />';
    }
    public static function deleteFiles(array $files): void
    {
        foreach($files as $file){
            echo $file;
            if(file_exists($file)){
                unlink($file);
            }
        }
    }

    public static function uploadImage($src): string
    {

        //uploads image to uploads
        //first as ChatGPT said explode the tag
        if($src){
            $imageData = explode(',', $src)[1];
            //nice got the path
            //now decode the image
            $decodedImage = base64_decode($imageData);
            $webpFileName = 'C:/xampp/htdocs/cms/admin/uploads/image_' . time() . '.webp';
//            $webpFileName = ABS_URL. 'C:/xampp/htdocs/cms/admin/uploads/image_' . time() . '.webp';
            $image = imagecreatefromstring($decodedImage);

            // Convert the image to WebP and save it
            if (imagewebp($image, $webpFileName)) {
                echo "Image successfully converted to WebP and saved as $webpFileName";
                $result = $webpFileName;

            }
            // Free up memory
            imagedestroy($image);
        }

        return $result;
    }

    public function getDataFieldsForEdit(): string
    {
        $out = "
    <label for='image" . $this->componentName . "' class='form-label'>" . $this->componentName . "</label>";

        if ($this->componentData) {
            $out .= '<p>';
            $out .= self::viewImage($this->componentData);
            $out .= '</p>';
        }

        $out .= '<img id="imagePreview' . $this->componentName . '" src="" class="img-thumbnail d-none" />';
        $out .= '<button class="btn btn-primary mt-3 d-none" id="cropBtn' . $this->componentName . '">Použít</button>';
        $out .= "<input class='d-none' type='text' id='dataPassImg" . $this->componentName . "' value='" . $this->componentData . "' name='component_" . $this->componentName . "' />";
        $out .= "<input onchange='handleImageUpload(this, \"" . $this->componentName . "\")' type='file' name='input_" . $this->componentName . "' class='form-control mt-3' id='image" . $this->componentName . "' accept='image/png, image/gif, image/jpeg, image/webp' />";

        return $out;
    }
    public function getDataFieldsForInsert(): string
    {
        $out = "
    <label for='image" . $this->componentName . "' class='form-label'>" . $this->componentName . "</label>";

        if ($this->componentData) {
            $out .= '<p>';
            $out .= self::viewImage($this->componentData);
            $out .= '</p>';
        }

        $out .= '<img id="imagePreview' . $this->componentName . '" src="" class="img-thumbnail d-none" />';
        $out .= '<button class="btn btn-primary mt-3 d-none" id="cropBtn' . $this->componentName . '">Použít</button>';
        $out .= "<input class='d-none' type='text' id='dataPassImg" . $this->componentName . "' value='" . $this->componentData . "' name='component_" . $this->componentName . "' />";
        $out .= "<input onchange='handleImageUpload(this, \"" . $this->componentName . "\")' type='file' name='input_" . $this->componentName . "' class='form-control mt-3' id='image" . $this->componentName . "' accept='image/png, image/gif, image/jpeg, image/webp' />";

        return $out;
    }
}