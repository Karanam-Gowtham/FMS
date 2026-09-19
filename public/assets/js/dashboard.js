function switchRole(baseUrl) {
    // Clear auth and go to role selection
    window.location.href = baseUrl + '/pages/select_role.php';
}

function activateRole(userRoleId, baseUrl, csrfToken) {
    // Use a form POST to switch role securely
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = baseUrl + '/pages/switch_role.php';

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
