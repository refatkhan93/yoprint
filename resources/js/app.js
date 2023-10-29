require('./bootstrap');
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const dropArea = document.getElementById('drop-area');

    // Prevent default behavior to open the file in the browser
    dropArea.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    // Handle file drop
    dropArea.addEventListener('drop', function(e) {
        e.preventDefault();
        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    // Handle file selection
    fileInput.addEventListener('change', function() {
        const files = fileInput.files;
        handleFiles(files);
    });
    fileInput.addEventListener('change', function() {
        const files = fileInput.files;
        handleFiles(files);
    });
    const fileLabel = document.getElementById('label');
        fileLabel.addEventListener('click', function() {
            fileInput.click();
    });
    function handleFiles(files) {
        for (const file of files) {
            if (file.type === 'application/vnd.ms-excel' || file.name.endsWith('.csv')) {
                $('#label').html('Selected: '+file.name);
                fileInput.files = files;
            }
            else
            {
                alert('Please select a valid CSV file.');
            }
        }
    }
});