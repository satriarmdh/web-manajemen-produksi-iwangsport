document.addEventListener("DOMContentLoaded", function () {
    const setupPasswordToggle = (inputId, buttonId, eyeId, eyeOffId) => {
        const passwordInput = document.getElementById(inputId);
        const togglePasswordBtn = document.getElementById(buttonId);
        const eyeIcon = document.getElementById(eyeId);
        const eyeOffIcon = document.getElementById(eyeOffId);

        if (!togglePasswordBtn || !passwordInput || !eyeIcon || !eyeOffIcon) {
            return;
        }

        togglePasswordBtn.addEventListener("click", function () {
            const isHidden = passwordInput.type === "password";
            passwordInput.type = isHidden ? "text" : "password";
            eyeIcon.classList.toggle("hidden", isHidden);
            eyeOffIcon.classList.toggle("hidden", !isHidden);
        });
    };

    setupPasswordToggle("password_input", "toggle_password_btn", "eye_icon", "eye_off_icon");
    setupPasswordToggle("password_confirmation_input", "toggle_password_confirmation_btn", "eye_confirmation_icon", "eye_confirmation_off_icon");
});