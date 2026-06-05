document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("password_input");
    const togglePasswordBtn = document.getElementById("toggle_password_btn");
    const eyeIcon = document.getElementById("eye_icon");
    const eyeOffIcon = document.getElementById("eye_off_icon");

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener("click", function () {
            // Ubah tipe input dari password ke text dan sebaliknya
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.add("hidden");
                eyeOffIcon.classList.remove("hidden");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("hidden");
                eyeOffIcon.classList.add("hidden");
            }
        });
    }
});