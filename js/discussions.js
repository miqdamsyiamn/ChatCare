/**
 * ChatCare - Discussions Management JavaScript
 * 
 * Handles discussion management functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle discussion form submission
    const discussionForm = document.getElementById('discussion-form');
    
    if (discussionForm) {
        discussionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const sessionId = formData.get('session_id');
            const isEdit = sessionId && sessionId !== '';
            
            // Validate form
            const title = formData.get('title');
            const participants = formData.getAll('participants[]');
            
            // Basic validation
            if (!title || title.trim() === '') {
                alert('Judul diskusi tidak boleh kosong');
                return;
            }
            
            if (participants.length === 0) {
                alert('Pilih setidaknya satu peserta');
                return;
            }
            
            // Disable form during submission
            const submitButton = discussionForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // Determine endpoint
            const endpoint = isEdit ? '/discussions/update' : '/discussions/create';
            
            // Send form data
            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to discussions list or chatroom
                    if (data.data && data.data.redirect_to_chatroom) {
                        window.location.href = `/chatroom?session_id=${data.data.session_id}`;
                    } else {
                        window.location.href = '/discussions';
                    }
                } else {
                    console.error('Error saving discussion:', data.message);
                    alert('Gagal menyimpan diskusi: ' + data.message);
                    
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan';
                }
            })
            .catch(error => {
                console.error('Error saving discussion:', error);
                alert('Gagal menyimpan diskusi. Silakan coba lagi.');
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Simpan';
            });
        });
    }
    
    // Handle delete discussion buttons
    const deleteButtons = document.querySelectorAll('.delete-discussion-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const sessionId = this.getAttribute('data-session-id');
            const title = this.getAttribute('data-title') || `Diskusi #${sessionId}`;
            
            if (confirm(`Apakah Anda yakin ingin menghapus diskusi "${title}"?`)) {
                // Disable button during deletion
                this.disabled = true;
                
                // Send delete request
                fetch('/discussions/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `session_id=${sessionId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove discussion row from table
                        const discussionRow = this.closest('tr');
                        discussionRow.remove();
                        
                        // Show success message
                        const alertContainer = document.getElementById('alert-container');
                        if (alertContainer) {
                            alertContainer.innerHTML = `
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                    <p>Diskusi "${title}" berhasil dihapus.</p>
                                </div>
                            `;
                            
                            // Auto-hide alert after 3 seconds
                            setTimeout(() => {
                                alertContainer.innerHTML = '';
                            }, 3000);
                        }
                    } else {
                        console.error('Error deleting discussion:', data.message);
                        alert('Gagal menghapus diskusi: ' + data.message);
                        
                        // Reset button
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error deleting discussion:', error);
                    alert('Gagal menghapus diskusi. Silakan coba lagi.');
                    
                    // Reset button
                    this.disabled = false;
                });
            }
        });
    });
    
    // Handle end discussion buttons
    const endButtons = document.querySelectorAll('.end-discussion-btn');
    
    endButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const sessionId = this.getAttribute('data-session-id');
            const title = this.getAttribute('data-title') || `Diskusi #${sessionId}`;
            
            if (confirm(`Apakah Anda yakin ingin mengakhiri diskusi "${title}"?`)) {
                // Disable button during operation
                this.disabled = true;
                
                // Send end request
                fetch('/discussions/end', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `session_id=${sessionId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update discussion status
                        const statusCell = this.closest('tr').querySelector('.status-cell');
                        if (statusCell) {
                            statusCell.innerHTML = '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Selesai</span>';
                        }
                        
                        // Replace end button with view report button
                        const actionCell = this.closest('td');
                        if (actionCell) {
                            actionCell.innerHTML = `
                                <a href="/reports/view?session_id=${sessionId}" class="text-blue-600 hover:text-blue-800 mr-2">
                                    <i class="fas fa-file-alt"></i> Lihat Laporan
                                </a>
                            `;
                        }
                        
                        // Show success message
                        const alertContainer = document.getElementById('alert-container');
                        if (alertContainer) {
                            alertContainer.innerHTML = `
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                                    <p>Diskusi "${title}" berhasil diakhiri.</p>
                                </div>
                            `;
                            
                            // Auto-hide alert after 3 seconds
                            setTimeout(() => {
                                alertContainer.innerHTML = '';
                            }, 3000);
                        }
                    } else {
                        console.error('Error ending discussion:', data.message);
                        alert('Gagal mengakhiri diskusi: ' + data.message);
                        
                        // Reset button
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error ending discussion:', error);
                    alert('Gagal mengakhiri diskusi. Silakan coba lagi.');
                    
                    // Reset button
                    this.disabled = false;
                });
            }
        });
    });
    
    // Handle discussion search
    const searchInput = document.getElementById('discussion-search');
    const discussionTable = document.getElementById('discussion-table');
    const discussionRows = discussionTable ? discussionTable.querySelectorAll('tbody tr') : [];
    
    if (searchInput && discussionRows.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            discussionRows.forEach(row => {
                const title = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const participants = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const date = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                
                if (title.includes(query) || participants.includes(query) || date.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('no-results');
            if (noResults) {
                let visibleRows = 0;
                discussionRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        visibleRows++;
                    }
                });
                
                noResults.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
    }
    
    // Handle status filter
    const statusFilter = document.getElementById('status-filter');
    
    if (statusFilter && discussionRows.length > 0) {
        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value.toLowerCase();
            
            discussionRows.forEach(row => {
                const statusCell = row.querySelector('.status-cell');
                if (!statusCell) return;
                
                const isActive = statusCell.textContent.toLowerCase().includes('aktif');
                
                if (selectedStatus === '' || 
                    (selectedStatus === 'active' && isActive) || 
                    (selectedStatus === 'completed' && !isActive)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('no-results');
            if (noResults) {
                let visibleRows = 0;
                discussionRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        visibleRows++;
                    }
                });
                
                noResults.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
    }
    
    // Handle participant selection
    const participantSelect = document.getElementById('participants');
    const selectedParticipantsContainer = document.getElementById('selected-participants');
    
    if (participantSelect && selectedParticipantsContainer) {
        // Initialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $(participantSelect).select2({
                placeholder: 'Pilih peserta diskusi',
                allowClear: true,
                width: '100%'
            });
        }
        
        // Update selected participants display
        function updateSelectedParticipants() {
            const selectedOptions = Array.from(participantSelect.selectedOptions);
            
            if (selectedOptions.length === 0) {
                selectedParticipantsContainer.innerHTML = '<p class="text-gray-500">Belum ada peserta yang dipilih</p>';
                return;
            }
            
            let html = '<div class="flex flex-wrap gap-2">';
            
            selectedOptions.forEach(option => {
                html += `
                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        ${option.text}
                    </div>
                `;
            });
            
            html += '</div>';
            
            selectedParticipantsContainer.innerHTML = html;
        }
        
        // Initial update
        updateSelectedParticipants();
        
        // Listen for changes
        participantSelect.addEventListener('change', updateSelectedParticipants);
        
        // For Select2, need to listen to its change event
        if (typeof $.fn.select2 !== 'undefined') {
            $(participantSelect).on('change', updateSelectedParticipants);
        }
    }
    
    // Handle date filtering
    const dateFilter = document.getElementById('date-filter');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const applyDateFilterBtn = document.getElementById('apply-date-filter');
    
    if (dateFilter && startDateInput && endDateInput && applyDateFilterBtn && discussionRows.length > 0) {
        applyDateFilterBtn.addEventListener('click', function() {
            const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
            const endDate = endDateInput.value ? new Date(endDateInput.value) : null;
            
            if (!startDate && !endDate) {
                // Reset filter if both dates are empty
                discussionRows.forEach(row => {
                    if (row.dataset.filteredByStatus !== 'hidden') {
                        row.style.display = '';
                    }
                });
                return;
            }
            
            discussionRows.forEach(row => {
                // Skip if already hidden by status filter
                if (row.dataset.filteredByStatus === 'hidden') return;
                
                const dateCell = row.querySelector('td:nth-child(3)');
                if (!dateCell) return;
                
                const dateText = dateCell.textContent.trim();
                const rowDate = new Date(dateText);
                
                let showRow = true;
                
                if (startDate && rowDate < startDate) {
                    showRow = false;
                }
                
                if (endDate) {
                    // Set end date to end of day
                    const endOfDay = new Date(endDate);
                    endOfDay.setHours(23, 59, 59, 999);
                    
                    if (rowDate > endOfDay) {
                        showRow = false;
                    }
                }
                
                row.style.display = showRow ? '' : 'none';
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('no-results');
            if (noResults) {
                let visibleRows = 0;
                discussionRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        visibleRows++;
                    }
                });
                
                noResults.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
        
        // Reset date filter
        const resetDateFilterBtn = document.getElementById('reset-date-filter');
        
        if (resetDateFilterBtn) {
            resetDateFilterBtn.addEventListener('click', function() {
                startDateInput.value = '';
                endDateInput.value = '';
                
                // Reset display for rows not hidden by status filter
                discussionRows.forEach(row => {
                    if (row.dataset.filteredByStatus !== 'hidden') {
                        row.style.display = '';
                    }
                });
                
                // Hide no results message if it was showing
                const noResults = document.getElementById('no-results');
                if (noResults) {
                    noResults.style.display = 'none';
                }
            });
        }
    }
});
