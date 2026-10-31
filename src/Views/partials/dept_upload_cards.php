<?php
/**
 * Department File Upload Quick Cards Partial
 */
$dept_upload_options = [
    ['sub' => 'Admin+Files', 'icon' => 'fa-user-tie', 'title' => 'Admin Files'],
    ['sub' => 'Faculty+Files', 'icon' => 'fa-chalkboard', 'title' => 'Faculty Files'],
    ['sub' => 'Student+Related+Files', 'icon' => 'fa-user-graduate', 'title' => 'Student Related Files'],
    ['sub' => 'Exam+Section+Files', 'icon' => 'fa-file-alt', 'title' => 'Exam Section Files'],
    ['type' => 'student_activity_file', 'icon' => 'fa-futbol', 'title' => 'Student Activities Files'],
];
?>
<div class="fms-card-grid">
    <?php foreach ($dept_upload_options as $opt): 
        $link = isset($opt['type'])
            ? BASE_URL . "/public/index.php?route=documents/upload&type=" . $opt['type']
            : BASE_URL . "/public/index.php?route=documents/upload&type=dept_file&sub_type=" . $opt['sub'];
    ?>
        <a href="<?= $link ?>" class="fms-action-card" style="border-left-color: #64748b;">
            <div class="fms-action-card-icon" style="background: #f8fafc; color: #64748b;">
                <i class="fas <?= $opt['icon'] ?>"></i>
            </div>
            <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($opt['title']) ?></div>
        </a>
    <?php endforeach; ?>
</div>
