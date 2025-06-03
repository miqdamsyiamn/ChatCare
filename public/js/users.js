/**
 * ChatCare - Users Management JavaScript
 * 
 * Handles user management functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle user form submission
    const userForm = document.getElementById('user-form');
    
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const userId = formData.get('user_id');
            const isEdit = userId && userId !== '';
            
            // Validate form
            const username = formData.get('username');
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            const role = formData.get('role');
            
            // Basic validation
            if (!username || username.trim() === '') {
                alert('Username tidak boleh kosong');
                return;
            }
            
            if (!isEdit && (!password || password.trim() === '')) {
                alert('Password tidak boleh kosong');
                return;
            }
            
            if (password && password !== confirmPassword) {
                alert('Password dan konfirmasi password tidak cocok');
                return;
            }
            
            if (!role) {
                alert('Peran pengguna harus dipilih');
                return;
            }
            
            // Disable form during submission
            const submitButton = userForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // Determine endpoint
            const endpoint = isEdit ? '/users/update' : '/users/create';
            
            // Send form data
            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to users list
                    window.location.href = '/users';
                } else {
                    console.error('Error saving user:', data.message);
                    alert('Gagal menyimpan pengguna: ' + data.message);
                    
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan';
                }
            })
            .catch(error => {
                console.error('Error saving user:', error);
                alert('Gagal menyimpan pengguna. Silakan coba lagi.');
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Simpan';
            });
        });
    }
    
    // Handle delete user buttons
    const deleteButtons = document.querySelectorAll('.delete-user-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const userId = this.getAttribute('data-user-id');
            const username = this.getAttribute('data-username');
            
            if (confirm(`Apakah Anda yakin ingin menghapus pengguna "${username}"?`)) {
                // Disable button during deletion
                this.disabled = true;
                
                // Send delete request
                fetch('/users/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `user_id=${userId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove user row from table
                        const userRow = this.closest('tr');
                        userRow.remove();
                        
                        // Show success message
                        const alertContainer = document.getElementById('alert-container');
                        if (alertContainer) {
                            alertContainer.innerHTML = `
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                    <p>Pengguna "${username}" berhasil dihapus.</p>
                                </div>
                            `;
                            
                            // Auto-hide alert after 3 seconds
                            setTimeout(() => {
                                alertContainer.innerHTML = '';
                            }, 3000);
                        }
                    } else {
                        console.error('Error deleting user:', data.message);
                        alert('Gagal menghapus pengguna: ' + data.message);
                        
                        // Reset button
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting user:', error);
                    alert('Gagal menghapus pengguna. Silakan coba lagi.');
                    
                    // Reset button
                    this.disabled = false;
                });
            }
        });
    });
    
    // Handle password visibility toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordField = document.getElementById(this.getAttribute('data-target'));
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordField.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
    
    // Handle user search
    const searchInput = document.getElementById('user-search');
    const userTable = document.getElementById('user-table');
    const userRows = userTable ? userTable.querySelectorAll('tbody tr') : [];
    
    if (searchInput && userRows.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            userRows.forEach(row => {
                const username = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const role = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (username.includes(query) || role.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('no-results');
            if (noResults) {
                let visibleRows = 0;
                userRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        visibleRows++;
                    }
                });
                
                noResults.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
    }
    
    // Handle role filter
    const roleFilter = document.getElementById('role-filter');
    
    if (roleFilter && userRows.length > 0) {
        roleFilter.addEventListener('change', function() {
            const selectedRole = this.value.toLowerCase();
            
            userRows.forEach(row => {
                const role = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (selectedRole === '' || role === selectedRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('no-results');
            if (noResults) {
                let visibleRows = 0;
                userRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        visibleRows++;
                    }
                });
                
                noResults.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
    }
    
    // Handle bulk actions
    const bulkActionForm = document.getElementById('bulk-action-form');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');
    
    if (bulkActionForm && userCheckboxes.length > 0 && selectAllCheckbox) {
        // Handle select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
            
            // Update bulk action button state
            updateBulkActionButton();
        });
        
        // Handle individual checkboxes
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Update select all checkbox
                let allChecked = true;
                userCheckboxes.forEach(cb => {
                    if (cb.closest('tr').style.display !== 'none' && !cb.checked) {
                        allChecked = false;
                    }
                });
                selectAllCheckbox.checked = allChecked;
                
                // Update bulk action button state
                updateBulkActionButton();
            });
        });
        
        // Handle bulk action form submission
        bulkActionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const action = document.getElementById('bulk-action').value;
            if (!action) {
                alert('Silakan pilih tindakan');
                return;
            }
            
            // Get selected user IDs
            const selectedUserIds = [];
            userCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedUserIds.push(checkbox.value);
                }
            });
            
            if (selectedUserIds.length === 0) {
                alert('Silakan pilih setidaknya satu pengguna');
                return;
            }
            
            // Confirm action
            if (!confirm(`Apakah Anda yakin ingin ${action === 'delete' ? 'menghapus' : 'mengubah peran'} ${selectedUserIds.length} pengguna yang dipilih?`)) {
                return;
            }
            
            // Disable form during submission
            const submitButton = bulkActionForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            
            // Prepare form data
            const formData = new FormData();
            formData.append('action', action);
            selectedUserIds.forEach(id => {
                formData.append('user_ids[]', id);
            });
            
            if (action === 'change_role') {
                const newRole = document.getElementById('new-role').value;
                if (!newRole) {
                    alert('Silakan pilih peran baru');
                    submitButton.disabled = false;
                    return;
                }
                formData.append('new_role', newRole);
            }
            
            // Send request
            fetch('/users/bulk-action', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show changes
                    window.location.reload();
                } else {
                    console.error('Error performing bulk action:', data.message);
                    alert('Gagal melakukan tindakan: ' + data.message);
                    
                    // Reset button
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error performing bulk action:', error);
                alert('Gagal melakukan tindakan. Silakan coba lagi.');
                
                // Reset button
                submitButton.disabled = false;
            });
        });
        
        // Function to update bulk action button state
        function updateBulkActionButton() {
            const bulkActionButton = document.getElementById('bulk-action-button');
            if (!bulkActionButton) return;
            
            let checkedCount = 0;
            userCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedCount++;
                }
            });
            
            bulkActionButton.disabled = checkedCount === 0;
            bulkActionButton.innerHTML = checkedCount > 0 ? 
                `Terapkan (${checkedCount})` : 'Terapkan';
        }
        
        // Handle bulk action selection
        const bulkActionSelect = document.getElementById('bulk-action');
        const newRoleContainer = document.getElementById('new-role-container');
        
        if (bulkActionSelect && newRoleContainer) {
            bulkActionSelect.addEventListener('change', function() {
                if (this.value === 'change_role') {
                    newRoleContainer.style.display = '';
                } else {
                    newRoleContainer.style.display = 'none';
                }
            });
        }
    }
});
