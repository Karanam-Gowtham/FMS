<?php
namespace App\Controllers;

class NAACCriterionController {
    public function show() {
        require_once __DIR__ . '/../../core/bootstrap.php';
        require_login();
        echo "<h2>NAAC Module</h2><p>NAAC criteria views are currently under development.</p>";
    }
}
