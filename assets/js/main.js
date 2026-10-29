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

/**
 * Universal Non-Negative Number Inputs & Calculation Guard
 * Enforces min="0", blocks minus keys, and clamps negative values across all forms and dynamically added elements.
 */
(function(window, document) {
    'use strict';
    if (window.__fms_non_negative_guard_installed__) return;
    window.__fms_non_negative_guard_installed__ = true;

    function isNumberInput(el) {
        if (!el || el.tagName !== 'INPUT') return false;
        if (el.hasAttribute('data-allow-negative')) return false;
        const type = (el.getAttribute('type') || '').toLowerCase();
        if (type === 'number') return true;
        if (el.classList.contains('calc-box') && type !== 'text' && type !== 'hidden') return true;
        if (el.classList.contains('t4a-input') || el.classList.contains('t4b-input') || el.classList.contains('t4c-input') || el.classList.contains('t441-input') || el.classList.contains('t451-input') || el.classList.contains('t461-input')) return true;
        if (el.hasAttribute('min') && parseFloat(el.getAttribute('min')) >= 0) return true;
        return false;
    }

    function enforceNonNegative(el) {
        if (!isNumberInput(el)) return;
        
        if (!el.hasAttribute('min')) {
            el.setAttribute('min', '0');
        }

        let val = el.value;
        if (typeof val === 'string' && val.length > 0) {
            if (val.includes('-')) {
                val = val.replace(/-/g, '');
                el.value = val;
            }
            const num = parseFloat(val);
            if (!isNaN(num) && num < 0) {
                el.value = '0';
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }

    function scanInputs() {
        const selector = 'input[type="number"], input.calc-box, input.t4a-input, input.t4b-input, input.t4c-input, input.t441-input, input.t451-input, input.t461-input, input[min]';
        document.querySelectorAll(selector).forEach(enforceNonNegative);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scanInputs);
    } else {
        scanInputs();
    }

    // 1. Block '-' and Minus keys on keydown
    document.addEventListener('keydown', function(e) {
        const target = e.target;
        if (isNumberInput(target)) {
            if (e.key === '-' || e.key === 'Minus' || e.code === 'NumpadSubtract' || e.keyCode === 189 || e.keyCode === 109) {
                e.preventDefault();
                e.stopPropagation();
            }
        }
    }, true);

    // 2. Intercept input event to clamp any negative values (handles spinner clicks & autofill)
    document.addEventListener('input', function(e) {
        enforceNonNegative(e.target);
    }, true);

    // 3. Intercept change and blur events
    document.addEventListener('change', function(e) {
        enforceNonNegative(e.target);
    }, true);
    document.addEventListener('blur', function(e) {
        enforceNonNegative(e.target);
    }, true);

    // 4. Intercept paste event
    document.addEventListener('paste', function(e) {
        const target = e.target;
        if (isNumberInput(target)) {
            const pasteData = (e.clipboardData || window.clipboardData) ? (e.clipboardData || window.clipboardData).getData('text') : '';
            if (pasteData && pasteData.includes('-')) {
                e.preventDefault();
                const sanitized = pasteData.replace(/-/g, '');
                const num = parseFloat(sanitized);
                if (!isNaN(num) && num >= 0) {
                    target.value = sanitized;
                    target.dispatchEvent(new Event('input', { bubbles: true }));
                    target.dispatchEvent(new Event('change', { bubbles: true }));
                } else if (!target.value) {
                    target.value = '0';
                }
            }
        }
    }, true);

    // 5. Observe DOM for dynamically created rows / modals
    if (window.MutationObserver && document.documentElement) {
        const observer = new MutationObserver(function(mutations) {
            for (let i = 0; i < mutations.length; i++) {
                const added = mutations[i].addedNodes;
                for (let j = 0; j < added.length; j++) {
                    const node = added[j];
                    if (node && node.nodeType === 1) {
                        if (isNumberInput(node)) {
                            enforceNonNegative(node);
                        }
                        if (node.querySelectorAll) {
                            node.querySelectorAll('input[type="number"], input.calc-box, input.t4a-input, input.t4b-input, input.t4c-input, input[min]').forEach(enforceNonNegative);
                        }
                    }
                }
            }
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }
})(window, document);

