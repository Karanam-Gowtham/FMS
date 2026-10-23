function switchRole(baseUrl) {
    // Go to role selection
    window.location.href = baseUrl + '/public/index.php?route=auth/select_role';
}

function activateRole(userRoleId, baseUrl, csrfToken) {
    if (typeof fetch === 'function') {
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
        return;
    }

    // Fallback: Use a form POST to switch role securely
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

    const csrf2 = document.createElement('input');
    csrf2.type = 'hidden';
    csrf2.name = 'csrf_token';
    csrf2.value = csrfToken;
    form.appendChild(csrf2);

    document.body.appendChild(form);
    form.submit();
}
