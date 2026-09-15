<?php
/**
 * CSRF Failure Test — simulates a POST to approve.php without valid token
 */
require_once __DIR__ . '/../core/bootstrap.php';

echo "=== CSRF Test ===\n\n";

// Test 1: csrfValidate() with no token should die
echo "Test: csrfValidate with no CSRF token in POST...\n";

// Simulate a POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['_csrf_token'] = 'invalid_garbage_token';
$_SESSION['_csrf_token'] = bin2hex(random_bytes(32)); // valid stored token

// csrfValidate compares submitted vs stored with hash_equals
// An invalid token should trigger die()
try {
    ob_start();
    csrfValidate();
    $output = ob_get_clean();
    echo "FAIL: csrfValidate did NOT reject invalid token\n";
} catch (\Throwable $e) {
    echo "PASS: csrfValidate rejected invalid token (die caught)\n";
}

// Test 2: Valid token should pass
echo "\nTest: csrfValidate with valid CSRF token...\n";
$valid_token = bin2hex(random_bytes(32));
$_POST['_csrf_token'] = $valid_token;
$_SESSION['_csrf_token'] = $valid_token;

// This should NOT die
csrfValidate();
echo "PASS: csrfValidate accepted valid token\n";

echo "\nDone.\n";
