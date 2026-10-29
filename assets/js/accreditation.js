/**
 * FMS Central Accreditation Client Library
 * 
 * Provides unified, DRY JavaScript services for both NBA (Criteria 1-9)
 * and NAAC (Criteria 1-7) accreditation workflows:
 *   - Universal Toast notifications (with auto-provisioning)
 *   - Asynchronous Auto-save orchestrator
 *   - Master & Final PDF generation workflows
 *   - Backward-compatible wrapper APIs
 */

(function(window, document) {
    'use strict';

    let toastTimer = null;

    /**
     * Display a consistent floating toast notification across all views.
     * Auto-creates the container in the DOM if not present.
     */
    function showToast(msg = 'Data saved successfully!', duration = 3000) {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'toast';
            toast.style.cssText = 'display: none; position: fixed !important; top: 90px !important; right: 24px !important; bottom: auto !important; left: auto !important; width: auto !important; max-width: 380px !important; height: auto !important; min-height: unset !important; padding: 12px 24px !important; border-radius: 8px !important; background: #10b981 !important; color: #fff !important; font-weight: 600 !important; font-size: 0.95rem !important; box-shadow: 0 10px 25px -3px rgba(0,0,0,0.3) !important; z-index: 99999 !important; pointer-events: none !important; box-sizing: border-box !important;';
            document.body.appendChild(toast);
        }

        toast.innerText = msg;
        toast.style.display = 'block';

        if (toastTimer) {
            clearTimeout(toastTimer);
        }
        toastTimer = setTimeout(() => {
            toast.style.display = 'none';
            toast.innerText = 'Data saved successfully!';
        }, duration);
    }

    /**
     * Resolves the active department ID from standard UI dropdowns or global state.
     */
    function getActiveDeptId() {
        const select = document.getElementById('level-select');
        if (select && select.value) {
            return String(select.value).replace(/^dept_/, '');
        }
        if (typeof window.activeDeptId !== 'undefined' && window.activeDeptId) {
            return String(window.activeDeptId).replace(/^dept_/, '');
        }
        return '1';
    }

    /**
     * Resolves the active academic year.
     */
    function getAcademicYear() {
        if (typeof window.year !== 'undefined' && window.year) {
            return String(window.year);
        }
        const select = document.getElementById('year');
        if (select && select.value) {
            return String(select.value);
        }
        return '2023-24';
    }

    /**
     * Resolves the active CSRF token.
     */
    function getCsrfToken() {
        if (window.FMS_CSRF_TOKEN) {
            return window.FMS_CSRF_TOKEN;
        }
        const input = document.querySelector('input[name="_csrf_token"], input[name="csrf_token"]');
        return input ? input.value : '';
    }

    /**
     * Safely executes whichever save function is active in the current view.
     */
    async function triggerActiveSave(silent = true) {
        if (typeof window.saveCurrentLevel === 'function') {
            try { window.saveCurrentLevel(); } catch (e) { console.warn('saveCurrentLevel hook:', e); }
        }

        if (typeof window.saveCriterionData === 'function') {
            return await window.saveCriterionData(silent);
        } else if (typeof window.saveData === 'function') {
            return await window.saveData(silent);
        }
        return true;
    }

    /**
     * Gathers payload objects defined across NBA/NAAC dynamic structures.
     */
    function getAccreditationPayload() {
        const payload = {};
        if (typeof window.levelData !== 'undefined') {
            payload.levelData = window.levelData;
        }
        if (typeof window.peos !== 'undefined') {
            payload.peos = window.peos;
        }
        if (typeof window.matrixData !== 'undefined') {
            payload.matrix = window.matrixData;
        }
        return payload;
    }

    /**
     * Unified Master / Final PDF Generator for all NBA and NAAC criteria.
     */
    async function generateAccreditationPDF(module = 'nba', critId = 1) {
        const btn = document.getElementById('btn-generate-pdf');
        const isNaac = (module === 'naac');
        const reportTitle = isNaac ? 'Final PDF' : 'Master PDF';

        let originalHtml = '';
        if (btn) {
            originalHtml = btn.innerHTML;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Generating ${reportTitle}...`;
            btn.disabled = true;
        }

        try {
            // Step 1: Auto-save active criterion data first
            const saved = await triggerActiveSave(true);
            if (!saved) {
                alert(`Could not auto-save criterion data prior to PDF generation. Please check inputs.`);
                if (btn) {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
                return;
            }

            // Step 2: Build Form Data
            const form = document.getElementById(isNaac ? 'naacForm' : 'nbaForm');
            const formData = form ? new FormData(form) : new FormData();

            const deptId = getActiveDeptId();
            const yearVal = getAcademicYear();
            const csrf = getCsrfToken();
            const payload = getAccreditationPayload();

            formData.append('_csrf_token', csrf);
            formData.append('csrf_token', csrf);
            formData.append('dept_id', deptId);
            formData.append('year', yearVal);
            formData.append('criterion_number', String(critId));
            if (Object.keys(payload).length > 0) {
                formData.append('json_data', JSON.stringify(payload));
            }

            // Step 3: Send generation request
            const baseUrl = window.FMS_BASE_URL || '';
            const apiUrl = `${baseUrl}/public/index.php?route=api/${module}/generate_pdf&id=${critId}`;

            const res = await fetch(apiUrl, {
                method: 'POST',
                body: formData
            });

            if (!res.ok) {
                throw new Error('Server returned HTTP ' + res.status);
            }

            const data = await res.json();
            if (data.success && data.pdf_url) {
                const container = document.getElementById('pdf-download-container');
                if (container) {
                    container.style.display = 'inline-flex';
                    container.innerHTML = `
                        <a href="${data.pdf_url}" target="_blank" class="btn-pdf-view" style="display: inline-flex; align-items: center; gap: 8px; color: #166534; background: #dcfce7; border: 1.5px solid #86efac; padding: 10px 20px; border-radius: 6px; font-weight: 700; text-decoration: none; font-size: 0.95rem; box-shadow: 0 2px 4px rgba(22, 101, 52, 0.1); transition: all 0.2s;">
                            <i class="fas fa-file-pdf" style="font-size: 1.15rem; color: #dc2626;"></i> View / Download ${reportTitle}
                        </a>
                    `;
                }
                showToast(`${reportTitle} Generated Successfully!`);
                window.open(data.pdf_url, '_blank');
            } else {
                alert('Error generating PDF: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (err) {
            console.error('Accreditation PDF Generation error:', err);
            alert('A network or server error occurred while generating the PDF: ' + err.message);
        } finally {
            if (btn) {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }
    }

    // Expose Global Public API & Backward-Compatible Aliases
    window.showToast = showToast;
    window.generateAccreditationPDF = generateAccreditationPDF;
    window.generateMasterPDF = function(critId = 1) {
        return generateAccreditationPDF('nba', critId);
    };
    window.generateFinalPDF = function(critId = null) {
        return generateAccreditationPDF('naac', critId || (window.crit_id || 1));
    };

})(window, document);
