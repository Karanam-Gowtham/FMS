<?php
/**
 * Shared Pagination Partial
 * 
 * Expected variables:
 * - int $total_pages: total number of available pages
 * - int $page_num: current active page number (1-indexed)
 * - string|null $extra_style: optional additional inline styles for container
 */

if (!isset($total_pages) || $total_pages <= 1) {
    return;
}

$page_num = isset($page_num) && is_numeric($page_num) ? (int)$page_num : 1;
$q = $_GET;
unset($q['page']);
$style_attr = !empty($extra_style) ? ' style="' . htmlspecialchars($extra_style, ENT_QUOTES, 'UTF-8') . '"' : '';
?>
<div class="pagination"<?= $style_attr ?>>
    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
        <a href="?<?= htmlspecialchars(http_build_query(array_merge($q, ['page' => $p])), ENT_QUOTES, 'UTF-8') ?>"
           class="<?= $p === $page_num ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
