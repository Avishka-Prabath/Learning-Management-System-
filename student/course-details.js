/**
 * LMS Course Details Interactive Scripts
 * Pure JavaScript (Vanilla JS)
 */

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. LIVE SEARCH / FILTER FOR LECTURE MATERIALS
    // ==========================================
    const searchInput = document.getElementById('materialSearchInput');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const query = this.value.toLowerCase().trim();
            const activeTab = document.querySelector('.tab-pane.active');
            
            if (activeTab) {
                const cards = activeTab.querySelectorAll('.content-card, .card');
                
                cards.forEach(card => {
                    const titleElement = card.querySelector('h6');
                    if (titleElement) {
                        const titleText = titleElement.textContent.toLowerCase();
                        if (titleText.includes(query)) {
                            card.parentElement.classList.remove('d-none');
                            card.classList.remove('d-none');
                        } else {
                            card.classList.add('d-none');
                        }
                    }
                });
            }
        });
    }


    // ==========================================
    // 2. ASSIGNMENT UPLOAD PREVIEW & VALIDATION
    // ==========================================
    const assignmentFileInput = document.getElementById('assignmentFileInput');
    const filePreviewArea = document.getElementById('filePreviewArea');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const fileSizeDisplay = document.getElementById('fileSizeDisplay');
    const submitBtn = document.getElementById('confirmSubmitBtn');

    if (assignmentFileInput) {
        assignmentFileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            
            if (file) {
                // File extension validation (.pdf, .zip, .docx only)
                const allowedExtensions = ['pdf', 'zip', 'docx', 'doc'];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                
                if (!allowedExtensions.includes(fileExtension)) {
                    alert('Invalid file format! Please upload a PDF, ZIP, or Word document.');
                    this.value = ''; // Reset input
                    if (filePreviewArea) filePreviewArea.classList.add('d-none');
                    if (submitBtn) submitBtn.disabled = true;
                    return;
                }

                // File size calculation (in MB)
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                if (fileSizeMB > 25) { // 25MB limit
                    alert('File size exceeds the 25MB limit!');
                    this.value = '';
                    if (filePreviewArea) filePreviewArea.classList.add('d-none');
                    if (submitBtn) submitBtn.disabled = true;
                    return;
                }

                // Show file info preview
                if (fileNameDisplay) fileNameDisplay.innerText = file.name;
                if (fileSizeDisplay) fileSizeDisplay.innerText = `${fileSizeMB} MB`;
                if (filePreviewArea) filePreviewArea.classList.remove('d-none');
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }

});


// ==========================================
// 3. LIVE VIDEO PLAYER MODAL LAUNCHER
// ==========================================
function playVideoModal(videoTitle, embedUrl) {
    const videoModalElement = document.getElementById('videoPlayerModal');
    const videoTitleElement = document.getElementById('modalVideoTitle');
    const videoIframe = document.getElementById('modalVideoIframe');

    if (videoModalElement && videoIframe) {
        if (videoTitleElement) videoTitleElement.innerText = videoTitle;
        
        // Set sample video URL or provided embed URL
        videoIframe.src = embedUrl || 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1';

        const videoModal = new bootstrap.Modal(videoModalElement);
        videoModal.show();

        // Clear video source when modal is closed (Stops playback audio)
        videoModalElement.addEventListener('hidden.bs.modal', function () {
            videoIframe.src = '';
        });
    }
}


// ==========================================
// 4. ASSIGNMENT SUBMISSION MODAL TRIGGER
// ==========================================
function openAssignmentModal(assignmentTitle) {
    const modalElement = document.getElementById('assignmentModal');
    const titleDisplay = document.getElementById('modalAssignmentTitle');

    if (modalElement) {
        if (titleDisplay) titleDisplay.innerText = assignmentTitle;
        const assignmentModal = new bootstrap.Modal(modalElement);
        assignmentModal.show();
    }
}