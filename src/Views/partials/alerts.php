<?php
/**
 * Shared Flash & Status Alerts Partial
 * 
 * Renders success, error, warning, and informational notices consistently across all views.
 * Supports controller-passed variables or direct $_SESSION flash values.
 */

$alert_success = $success_msg ?? $disp_success ?? ($_SESSION['success_msg'] ?? null);
$alert_error   = $error_msg ?? $disp_error ?? ($_SESSION['error_msg'] ?? null);
$alert_warning = $warning_msg ?? ($_SESSION['warning_msg'] ?? null);
$alert_info    = $info_msg ?? ($_SESSION['info_msg'] ?? null);

// Clear session flash messages to avoid stale display on next page loads
unset($_SESSION['success_msg'], $_SESSION['error_msg'], $_SESSION['warning_msg'], $_SESSION['info_msg']);
?>

<?php if (!empty($alert_success)): ?>
    <div class="fms-alert fms-alert-success">
        <div class="fms-alert-content">
            <i class="fas fa-check-circle" style="font-size: 1.1rem; color: #10b981;"></i>
            <span><?= htmlspecialchars((string)$alert_success, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <button type="button" class="fms-alert-dismiss" onclick="this.closest('.fms-alert').remove()" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>

<?php if (!empty($alert_error)): ?>
    <div class="fms-alert fms-alert-error">
        <div class="fms-alert-content">
            <i class="fas fa-exclamation-circle" style="font-size: 1.1rem; color: #ef4444;"></i>
            <span><?= htmlspecialchars((string)$alert_error, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <button type="button" class="fms-alert-dismiss" onclick="this.closest('.fms-alert').remove()" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>

<?php if (!empty($alert_warning)): ?>
    <div class="fms-alert fms-alert-warning">
        <div class="fms-alert-content">
            <i class="fas fa-exclamation-triangle" style="font-size: 1.1rem; color: #f59e0b;"></i>
            <span><?= htmlspecialchars((string)$alert_warning, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <button type="button" class="fms-alert-dismiss" onclick="this.closest('.fms-alert').remove()" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>

<?php if (!empty($alert_info)): ?>
    <div class="fms-alert fms-alert-info">
        <div class="fms-alert-content">
            <i class="fas fa-info-circle" style="font-size: 1.1rem; color: #3b82f6;"></i>
            <span><?= htmlspecialchars((string)$alert_info, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <button type="button" class="fms-alert-dismiss" onclick="this.closest('.fms-alert').remove()" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>
