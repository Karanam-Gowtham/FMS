document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger && navMenu) {
        console.log("Elements found");
        hamburger.addEventListener('click', () => {
            console.log("Hamburger clicked");
            navMenu.classList.toggle('active');
            console.log("Active class toggled");
        });
    } else {
        console.error("Elements not found");
    }
});
