<?php
/**
 * Shared Accreditation Action Bar Partial
 * 
 * Renders the standardized bottom action toolbar for all accreditation criteria (NBA & NAAC).
 * Eliminates HTML/CSS duplication across views while preserving individual form hooks.
 * 
 * Parameters (passed or inherited from view scope):
 *  - $crit_id (int)               Criterion number (1..9 for NBA, 1..7 for NAAC)
 *  - $module (string)             'nba' or 'naac' (default: 'nba')
 *  - $existing_pdf_url (string)   URL to download existing PDF (or null)
 *  - $save_onclick (string)       JavaScript function to call on save (e.g. 'saveCriterionData()' or 'saveData()')
 *  - $save_label (string)         Optional label for save button (default: "Save Criterion X")
 */

$module = $module ?? 'nba';
$crit_id = (int)($crit_id ?? 1);
$existing_pdf_url = $existing_pdf_url ?? null;
$is_naac = ($module === 'naac');

if (!isset($save_onclick)) {
    $save_onclick = $is_naac ? 'saveCriterionData()' : 'saveData()';
}
if (!isset($save_label)) {
    $save_label = "Save Criterion {$crit_id}";
}

$pdf_btn_text = $is_naac ? 'Generate Final PDF' : 'Generate Master PDF';
$pdf_view_text = $is_naac ? 'View / Download Final PDF' : 'View / Download Master PDF';
?>

<div class="action-footer-bar" style="margin-top: 35px; padding-top: 25px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 4rem;">
    <div id="pdf-download-container" style="<?= !empty($existing_pdf_url) ? 'display: inline-flex;' : 'display: none;' ?>">
        <?php if (!empty($existing_pdf_url)): ?>
            <a href="<?= htmlspecialchars($existing_pdf_url) ?>" target="_blank" class="btn-pdf-view" style="display: inline-flex; align-items: center; gap: 8px; color: #166534; background: #dcfce7; border: 1.5px solid #86efac; padding: 10px 20px; border-radius: 6px; font-weight: 700; text-decoration: none; font-size: 0.95rem; box-shadow: 0 2px 4px rgba(22, 101, 52, 0.1); transition: all 0.2s;">
                <i class="fas fa-file-pdf" style="font-size: 1.15rem; color: #dc2626;"></i> <?= htmlspecialchars($pdf_view_text) ?>
            </a>
        <?php endif; ?>
    </div>
    <div style="display: flex; align-items: center; gap: 15px; margin-left: auto;">
        <button type="button" class="btn-primary" id="btn-generate-pdf" onclick="generateAccreditationPDF('<?= $module ?>', <?= $crit_id ?>)" style="background: #0284c7; border: none; border-radius: 6px; padding: 10px 22px; color: white; cursor: pointer; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2); transition: background 0.2s;">
            <i class="fas fa-file-pdf"></i> <?= htmlspecialchars($pdf_btn_text) ?>
        </button>
        <button type="button" class="btn-save" onclick="<?= htmlspecialchars($save_onclick) ?>"><?= htmlspecialchars($save_label) ?></button>
    </div>
</div>
