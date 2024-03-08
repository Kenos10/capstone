//Dashboard Section ===
let toggleContainer = document.querySelector('#container');
let toggleMenu = document.querySelector('#menu-toggle');
let toggleGrid = document.querySelector('main');
let overlay = document.querySelector('#overlay');

toggleMenu.addEventListener('click',function(e){
        toggleContainer.classList.toggle('active');
        toggleGrid.classList.toggle('container-active');
        overlay.classList.toggle('overlay');
});

//Profile menu ===
let toggleProfile = document.querySelector('#profile-menu');
let toggleShow = document.querySelector('#toggle-acc');

document.addEventListener("click", e => {
        if (toggleProfile.contains(e.target)) {
                toggleShow.classList.toggle("show");
        } else if (!toggleShow.contains(e.target)) {
                toggleShow.classList.remove("show");
        }
});