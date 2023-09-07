document.addEventListener("DOMContentLoaded", function () {
    const roleSelects = document.querySelectorAll('select');
    roleSelects.forEach(select => {
        const originalRole = select.getAttribute('data-original-role');
        
        select.addEventListener('change', (event) => {
            const selectedSelect = event.target; // Get the specific select that triggered the change
            const validateLink = document.querySelector(`.validate-user-icon[data-user-id="${selectedSelect.dataset.userId}"]`);
            const cancelLink = document.querySelector(`.cancel-user-icon[data-user-id="${selectedSelect.dataset.userId}"]`);
            
            if (selectedSelect.value !== originalRole) {
                validateLink.style.display = 'inline';
                cancelLink.style.display = 'inline';
            } else {
                validateLink.style.display = 'none';
                cancelLink.style.display = 'none';
            }

            validateLink.addEventListener('click', (event) => {
                event.preventDefault();
                const userId = event.target.getAttribute('data-user-id');
                const selectedRole = selectedSelect.value
        
                // Send AJAX request to update user role
                fetch(`/admin/users-manage/edit-level/${userId}/${selectedRole}`, {
                    method: 'POST',
                    headers : { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Role updated'); // Console log the success message
                    console.log(data)
                    // Disable the select element
                    selectedSelect.disabled = true;

                    // Hide the "Valider" link
                    validateLink.style.display = 'none';
                    cancelLink.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error in updating', error); // Console log the error message
                });
            })

            cancelLink.addEventListener('click', (event) => {
                event.preventDefault();
                
                // Disable the select element
                selectedSelect.disabled = true;

                validateLink.style.display = 'none';
                cancelLink.style.display = 'none';
            });
        });
    });
    
    const editLinks = document.querySelectorAll('.edit-user-icon');
    editLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const userId = link.getAttribute('data-user-id');
            const roleSelect = document.getElementById(`user-role-${userId}`);
            roleSelect.removeAttribute('disabled');
            roleSelect.dataset.userId = userId; // Store the user ID
        });
    });
  });