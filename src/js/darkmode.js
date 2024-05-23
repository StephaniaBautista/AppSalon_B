function darkMode() {
  const btnDarkMode = document.querySelector(".dark-mode-boton");
  btnDarkMode.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");
  });
}

function aumenta() {
  const aumenta = document.querySelector(".aumento-size");
  aumenta.addEventListener("click", function () {
    document.body.classList.add("aumenta");
  });
}

function disminuye() {
  const disminuye = document.querySelector(".disminuye-size");
  disminuye.addEventListener("click", function () {
    document.body.classList.add("disminuye");
  });
}

darkMode();
aumenta();
disminuye();
