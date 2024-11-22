// Function to handle form submissions
function handleFormSubmit(formId, action) {
    const form = document.getElementById(formId);
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        formData.append('action', action);

        fetch('api/handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Success!', 'success');
            } else {
                showAlert('An error occurred.', 'danger');
            }
        })
        .catch(error => {
            showAlert('An error occurred.', 'danger');
            console.error('Error:', error);
        });
    });
}

// Function to show alerts
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.querySelector('.main-content').insertBefore(
        alertDiv,
        document.querySelector('.main-content').firstChild
    );

    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Initialize all form handlers
document.addEventListener('DOMContentLoaded', function() {
    handleFormSubmit('logoForm', 'update_logo');
    handleFormSubmit('whatsappForm', 'update_whatsapp');
    // Add more form handlers as needed
}); 