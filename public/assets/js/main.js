
const menuToggle = document.getElementById('menuToggle');
const navList = document.getElementById('navList');

if (menuToggle && navList) {
    menuToggle.addEventListener('click', function () {
        navList.classList.toggle('show');
    });
}
