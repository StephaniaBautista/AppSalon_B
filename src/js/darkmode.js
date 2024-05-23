function darkMode() {
  const btnDarkMode = document.querySelector(".dark-mode-boton");
  btnDarkMode.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");
  });
}

darkMode();
