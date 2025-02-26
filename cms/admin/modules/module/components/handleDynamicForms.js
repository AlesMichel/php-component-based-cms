function handleDynamicForm() {
    const componentId = document.getElementById('createComponentSelect').value;
    // Create an AJAX request to PHP
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'loadComponentFields.php', true); // Replace with your PHP endpoint
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('dynamic-fields').innerHTML = xhr.responseText;
        }
    };
    xhr.send('component_id=' + componentId);
}