let toggleInvisible = document.querySelector('#invisible');
let toggleVisible = document.querySelector('#visible');

toggleInvisible.addEventListener("click", e => {
        toggleInvisible.classList.toggle("hidden");
        toggleVisible.classList.remove("hidden");
        showPassword();
});

toggleVisible.addEventListener("click", e => {  
        toggleInvisible.classList.remove("hidden");
        toggleVisible.classList.toggle("hidden");
        showPassword();

});

function showPassword() {
    var x = document.getElementById("password");
    if (x.type === "password") {
            x.type = "text";
    } else {
            x.type = "password";
    }
}