const menuBtn = document.querySelector("#mobile-menu");
const cerrarBtn = document.querySelector("#cerrar-menu");
const sidebar = document.querySelector(".sidebar");

if (menuBtn) {
  menuBtn.addEventListener("click", () => {
    sidebar.classList.add("mostrar");
  });
}

if (cerrarBtn) {
  cerrarBtn.addEventListener("click", () => {
    sidebar.classList.add("ocultar");

    setTimeout(() => {
      sidebar.classList.remove("mostrar");
      sidebar.classList.remove("ocultar");
    }, 500);
  });
}

// Elimina la clase de mostrar en un tamaño de tablet o superior
window.addEventListener("resize", () => {
  const anchoPantalla = document.body.clientWidth;
  if (anchoPantalla <= 750) {
    sidebar.classList.remove("mostrar");
  }
});
