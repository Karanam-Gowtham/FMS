<?php
/**
 * Quick Achievement Upload Cards Partial
 */
$quick_achievements = [
    ['type' => 'journal', 'color' => '#3b82f6', 'bg' => '#eff6ff', 'icon' => 'fa-book-open', 'title' => 'Research Papers Published'],
    ['type' => 'conference', 'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'icon' => 'fa-users', 'title' => 'Conference Proceedings'],
    ['type' => 'patent', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'fa-certificate', 'title' => 'Patents'],
    ['type' => 'fdp_attended', 'color' => '#10b981', 'bg' => '#ecfdf5', 'icon' => 'fa-chalkboard-teacher', 'title' => 'FDP Attended'],
    ['type' => 'fdp_organised', 'color' => '#ec4899', 'bg' => '#fdf2f8', 'icon' => 'fa-bullhorn', 'title' => 'FDP Organized'],
    ['type' => 'conf_organised', 'color' => '#06b6d4', 'bg' => '#ecfeff', 'icon' => 'fa-microphone', 'title' => 'Conference Organized'],
];
?>
<div class="fms-card-grid">
    <?php foreach ($quick_achievements as $qa): ?>
        <a href="<?= BASE_URL ?>/public/index.php?route=documents/upload&amp;type=<?= $qa['type'] ?>" class="fms-action-card" style="border-left-color: <?= $qa['color'] ?>;">
            <div class="fms-action-card-icon" style="background: <?= $qa['bg'] ?>; color: <?= $qa['color'] ?>;">
                <i class="fas <?= $qa['icon'] ?>"></i>
            </div>
            <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($qa['title']) ?></div>
        </a>
    <?php endforeach; ?>
</div>
