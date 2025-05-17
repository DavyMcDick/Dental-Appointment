function clearError() {
    let errorMessage = document.getElementById("error-message");
    if (errorMessage) {
        errorMessage.style.display = "none"; // Hide error message when typing
    }
}


function togglePassword() {
    let passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}