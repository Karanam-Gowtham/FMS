<?php
// User requested to remove CSRF.
// These functions are kept as no-ops so we don't have to remove them from all files.

function csrfToken(): string
{
    return '';
}

function csrfField(): string
{
    return '';
}

function csrfValidate(): void
{
    // No-op
}
?>
