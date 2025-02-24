// Wait for the document to load
document.addEventListener('DOMContentLoaded', function() {
    // Select the alert element
    const alert = document.getElementById('cmsAlert');
    // Check if the alert exists
    if (alert) {
        // Set a timeout to fade out the alert after 3 seconds
        setTimeout(function() {
            alert.classList.remove('show');
            alert.classList.add('fade');

            // Optionally remove the alert from the DOM after fading
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500); // Wait for the fade-out transition (0.5 seconds)
        }, 3000); // 3 seconds delay before fading
    }
});