<?php
include_once 'includes/header.php';
?>
<nav class="navbar" style="position: sticky; top: 70px; margin-top: 0; background: white;">
    Navbar
</nav>
<div id="out"></div>
<script>
    window.onload = function() {
        var h = document.querySelector('.main-header-navbar');
        var n = document.querySelector('.navbar');
        var out = document.getElementById('out');
        out.innerHTML = "Header height: " + h.offsetHeight + "<br>" +
                        "Header top: " + h.getBoundingClientRect().top + "<br>" +
                        "Navbar top: " + n.getBoundingClientRect().top + "<br>" +
                        "Navbar margin-top: " + window.getComputedStyle(n).marginTop + "<br>";
    }
</script>
