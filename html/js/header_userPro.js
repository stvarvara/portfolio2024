"use strict";
window.addEventListener("DOMContentLoaded", (event) => {
  //menu utilisateur
  const headerUser = document.getElementById("header__info");
  const menu = document.getElementById("menu-user");
  var fermerMenu = document.getElementById("fermerMenu");

  //menu utilisateur
  if (headerUser) {
    headerUser.addEventListener("click", function () {
      console.log("Clicked on header user");
      menu.style.display =
        menu.style.display === "none" || menu.style.display === ""
          ? "inline-flex"
          : "none";
    });
  }

  window.addEventListener("click", function (e) {
    if (
      headerUser &&
      (e.target === headerUser || headerUser.contains(e.target))
    )
      return;
    if (e.target === fermerMenu || !menu.contains(e.target)) {
      menu.style.display = "none";
    }
  });
});
