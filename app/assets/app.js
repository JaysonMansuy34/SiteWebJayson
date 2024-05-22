import "./scss/app.scss";
const bootstrap = require("bootstrap");
import AOS from "aos";
import "aos/dist/aos.css"; 
import VanillaTilt from "vanilla-tilt";


// Details Profile information card

VanillaTilt.init(document.querySelectorAll(".tilt-card"), {
  max: 10,
  speed: 800,
  glare: true,
  "max-glare": 0.1,
  scale: 1,
});

AOS.init();


// RELOAD EN CAS ERREUR FORMULAIRE CONTACTER

document.addEventListener("DOMContentLoaded", function () {
  if (document.body.classList.contains("show-contact-modal")) {
    const contactModal = new bootstrap.Modal(
      document.getElementById("contactModal")
    );
    contactModal.show();
    fetch("/clear-session-flag")
      .then(() => {
        console.log("valide");
      })
      .catch((error) => {
        console.error("erreur:", error);
      });
  }
});

