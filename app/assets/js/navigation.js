document.addEventListener('DOMContentLoaded', function () {
    const menuButton = document.querySelector('.navbar-toggler');
    const menuIcon = document.getElementById('menu-icon');
    const navbar = document.getElementById('navbarSupportedContent');

    menuButton.addEventListener('click', function () {
        navbar.addEventListener('shown.bs.collapse', function () {
            menuIcon.src = '/images/close.svg';
        });

        navbar.addEventListener('hidden.bs.collapse', function () {
            menuIcon.src = '/images/menu.svg';
        });
    });

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
        } else {
            document.getElementById("myBtn").style.display = "none";
        }
    }

    window.onscroll = scrollFunction;

    // Lorsque l'utilisateur clique sur le bouton, défiler jusqu'en haut du document
    function topFunction() {
        document.body.scrollTop = 0; // Pour Safari
        document.documentElement.scrollTop = 0; // Pour Chrome, Firefox, IE et Opera
    }

    const myBtn = document.getElementById("myBtn");
    if (myBtn) {
        myBtn.onclick = topFunction;
    }

    let today = new Date();
    let options = { day: 'numeric', month: 'long', year: 'numeric' };
    let dateString = today.toLocaleDateString('fr-FR', options);

    let words = [
        'Bienvenue sur le site !',
        'Je me présente.',
        'Je m\'appelle Jayson.',
        'Développeur Junior.',
        'Actuellement le ' + dateString
    ],
    part,
    i = 0,
    offset = 0,
    len = words.length,
    forwards = true,
    skip_count = 0,
    skip_delay = 15,
    speed = 70;

    function wordflick() {
        setInterval(function () {
            if (forwards) {
                if (offset >= words[i].length) {
                    ++skip_count;
                    if (skip_count == skip_delay) {
                        forwards = false;
                        skip_count = 0;
                    }
                }
            } else {
                if (offset == 0) {
                    forwards = true;
                    i++;
                    offset = 0;
                    if (i >= len) {
                        i = 0;
                    }
                }
            }
            part = words[i].substr(0, offset);
            if (skip_count == 0) {
                if (forwards) {
                    offset++;
                } else {
                    offset--;
                }
            }
            document.querySelector('.word').textContent = part;
        }, speed);
    }

    wordflick();
});
function setCurrentYear() {
    document.getElementById('current-year').textContent = new Date().getFullYear();
}

window.onload = setCurrentYear;


