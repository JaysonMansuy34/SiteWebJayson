document.addEventListener('DOMContentLoaded', function () {
    const loadingScreen = document.getElementById('loading-screen');
    const formContainer = document.querySelector('.container');
    const loginForm = formContainer.querySelector('form');
    const loginButton = document.getElementById('login-button');
    const chart = document.querySelector('.chart');
    const load = document.querySelector('.load');
    const steps = document.querySelectorAll('.form-step');
    let currentStep = 0;

    function showStep(step) {
        steps.forEach((s, index) => {
            s.style.display = index === step ? 'block' : 'none';
        });
    }

    function increase() {
        let SPEED = 40;
        let limit = parseInt(document.getElementById("value1").textContent, 10);
        let i = 0;

        function progress() {
            if (i <= limit) {
                document.getElementById("value1").textContent = i + "%";
                setTimeout(progress, SPEED);
                i++;
            }
        }

        progress();
    }

    loginButton.addEventListener('click', async function (e) {
        e.preventDefault();

        if (loginForm.email.value && loginForm.password.value) {
            formContainer.classList.add('animate__backOutUp');
            setTimeout(() => {
                document.getElementById('form-container').style.display = 'none';
                chart.style.display = 'flex';
                load.classList.add('vh-100');
                increase(); // Start the progress animation
            }, 700);

            loginForm.submit(); // Submit the form immediately after the animation starts
        } else {
            alert("Veuillez remplir tous les champs.");
        }
    });

    setTimeout(() => {
        loadingScreen.style.display = 'none';
        formContainer.style.display = 'block';
        showStep(currentStep);
    }, 2000);
});

document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('inputEmail');
    const passwordInput = document.getElementById('inputPassword');
    const loginButton = document.getElementById('login-button');

    loginButton.disabled = true; // Disable the login button initially

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function checkFields() {
        const isEmailValid = isValidEmail(emailInput.value);
        const isPasswordFilled = passwordInput.value.trim() !== '';
        loginButton.disabled = !isEmailValid || !isPasswordFilled;
    }

    emailInput.addEventListener('input', checkFields);
    passwordInput.addEventListener('input', checkFields);
});
