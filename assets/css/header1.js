document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger && navMenu) {

        hamburger.addEventListener('click', () => {

            navMenu.classList.toggle('active');

        });
    } else {
        console.error("Elements not found");
    }
});
