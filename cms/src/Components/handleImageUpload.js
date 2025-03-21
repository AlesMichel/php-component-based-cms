function handleImageUpload(inputElement, componentName) {
    // Reset previous variables
    const previewId = 'imagePreview' + componentName;
    const cropBtnId = 'cropBtn' + componentName;
    cropBtnId.toString()
    console.log(cropBtnId)
    const hiddenInputId = 'dataPassImg' + componentName;
    // Resetting the preview image
    const previewImage = document.getElementById(previewId);
    const cropBtn = document.getElementById(cropBtnId);
    const hiddenInput = document.getElementById(hiddenInputId);

    // Clear previous state
    if (previewImage) {
        previewImage.src = ''; // Clear the previous image
        previewImage.style.display = 'none'; // Hide the image
        previewImage.classList.add('d-none'); // Add the hidden class
    }

    if (hiddenInput) {
        hiddenInput.value = ''; // Clear the hidden input value
    }

    // Continue with the image upload process
    if (inputElement.files && inputElement.files[0]) {
        const fileReader = new FileReader();

        fileReader.onload = function(e) {
            if (previewImage) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block'; // Show the image
                previewImage.classList.remove('d-none'); // Remove the hidden class

                // Initialize Cropper.js
                const cropper = new Cropper(previewImage, {
                    viewMode: 1,
                    autoCropArea: 0.5,
                    ready() {
                        console.log('Cropper is ready!');

                        console.log(cropBtn);
                        cropBtn.classList.remove('d-none');
                    }
                });

                // When the user clicks the crop button
                cropBtn.onclick = function(event) {
                    event.preventDefault(); // Prevent form submission

                    const cropBoxData = cropper.getCropBoxData();
                    const canvasData = cropper.getCroppedCanvas();
                    const croppedImageDataUrl = canvasData.toDataURL('image/png');
                    console.log(croppedImageDataUrl);

                    // Set the base64 data into the hidden input field
                    if (hiddenInput) {
                        hiddenInput.value = croppedImageDataUrl; // Store the cropped image data
                    }

                    console.log('Cropped image data set in hidden input.');
                };
            }
        };

        fileReader.readAsDataURL(inputElement.files[0]);
    }
}