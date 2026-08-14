/**
 * FMS Global Interaction Scripts
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Page Load Transition
    document.body.classList.add('fms-loaded');

    // 2. Prevent Double Submissions & Show Loading State
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Find submit button inside this form
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitBtn) {
                // If it's already loading, prevent duplicate submission
                if (submitBtn.classList.contains('is-loading')) {
                    e.preventDefault();
                    return;
                }
                
                // Set loading state visually
                submitBtn.classList.add('is-loading');
                
                if (submitBtn.tagName.toLowerCase() === 'input') {
                    const originalText = submitBtn.value;
                    submitBtn.setAttribute('data-original-text', originalText);
                    submitBtn.value = 'Processing...';
                    
                    // Reset after 4 seconds (handles file downloads staying on page)
                    setTimeout(() => {
                        submitBtn.classList.remove('is-loading');
                        submitBtn.value = originalText;
                    }, 4000);
                } else {
                    const originalHTML = submitBtn.innerHTML;
                    submitBtn.setAttribute('data-original-html', originalHTML);
                    
                    if (submitBtn.children.length === 0) {
                        submitBtn.innerHTML = 'Processing... <span class="spinner"></span>';
                    } else {
                        // If button has complex HTML (like cards), just append spinner
                        submitBtn.insertAdjacentHTML('beforeend', ' <span class="spinner" style="position:absolute; right:20px; top:50%; transform:translateY(-50%);"></span>');
                        submitBtn.style.position = 'relative';
                    }
                    
                    // Reset after 4 seconds (handles file downloads staying on page)
                    setTimeout(() => {
                        submitBtn.classList.remove('is-loading');
                        submitBtn.innerHTML = originalHTML;
                    }, 4000);
                }
            }
        });
    });
});

/**
 * showToast: Displays a modern toast notification on the screen
 * @param {string} message - The message to display
 * @param {string} type - 'success', 'error', or 'info'
 */
function showToast(message, type = 'info') {
    // Ensure container exists
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fms-toast toast-${type}`;
    
    // Icon based on type
    let icon = '';
    if (type === 'success') {
        icon = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
    } else if (type === 'error') {
        icon = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
    } else {
        icon = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    }

    toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-message">${message}</div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;

    // Add to container
    container.appendChild(toast);

    // Trigger reflow to start animation
    void toast.offsetWidth;
    toast.classList.add('show');

    // Remove after 4 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        toast.addEventListener('transitionend', () => {
            toast.remove();
        });
    }, 4000);
}
