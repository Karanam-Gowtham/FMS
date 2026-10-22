function switchRole(baseUrl) {
    // Go to role selection
    window.location.href = baseUrl + '/public/index.php?route=auth/select_role';
}

function activateRole(userRoleId, baseUrl, csrfToken) {
    // Use a form POST to switch role securely
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = baseUrl + '/public/index.php?route=auth/select_role';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'user_role_id';
    input.value = userRoleId;
    form.appendChild(input);

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_csrf_token';
    csrf.value = csrfToken;
    form.appendChild(csrf);

    document.body.appendChild(form);
    form.submit();
}
