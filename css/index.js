// Add smooth scrolling for navigation
document.querySelectorAll('nav .nav-btn').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const section = button.textContent.trim().toLowerCase();
        // You can add scroll behavior when sections are added
    });
});