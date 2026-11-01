<?php if (!empty($user_roles) && count($user_roles) > 1): ?>
<div style="margin-bottom: 2rem;">
    <p style="font-weight: 600; margin-bottom: 10px; color: #475569;">Switch Role:</p>
    <?php foreach ($user_roles as $ur): ?>
        <?php 
            $ur_id = (int)($ur['user_role_id'] ?? $ur['role_id'] ?? 0);
            $curr_id = (int)($active_role['user_role_id'] ?? $active_role['role_id'] ?? 0);
            if (!$active_role || $ur_id !== $curr_id): 
        ?>
            <button class="switch-role-btn" onclick="activateRole(<?= $ur_id ?>, '<?= BASE_URL ?>', '<?= csrfToken() ?>')">
                Switch to <?= htmlspecialchars($ur['role_name'] ?? '') ?> (<?= htmlspecialchars($ur['dept_name'] ?? '') ?>)
            </button>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<script>
function activateRole(userRoleId, baseUrl, csrfToken) {
    fetch(baseUrl + '/public/index.php?route=auth/select_role', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: 'user_role_id=' + encodeURIComponent(userRoleId) + '&_csrf_token=' + encodeURIComponent(csrfToken) + '&csrf_token=' + encodeURIComponent(csrfToken)
    }).then(async response => {
        if (!response.ok) {
            let errorText = 'Server returned HTTP ' + response.status;
            try {
                const data = await response.json();
                if (data && data.message) errorText = data.message;
            } catch (_) {}
            throw new Error(errorText);
        }
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            try {
                const resData = await response.json();
                window.location.href = resData.redirect_url || (baseUrl + '/public/index.php?route=dashboard');
            } catch (_) {
                window.location.href = baseUrl + '/public/index.php?route=dashboard';
            }
        }
    }).catch(err => {
        console.error('Role switch failed:', err);
        alert(err.message || 'Failed to switch role. Please check network connection and try again.');
    });
}
</script>
<?php endif; ?>
