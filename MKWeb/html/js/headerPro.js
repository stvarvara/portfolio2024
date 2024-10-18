"use strict";
window.addEventListener("DOMContentLoaded", (event) => {
  // menu general
  const LeMenu = document.getElementById("LeMenu");


  const btnDisconnect = document.getElementById("disconnect");

  // menu general

  CmdMenu.addEventListener("click", function () {
    if (LeMenu.style.display === "none") {
      LeMenu.style.display = "";
    } else {
      LeMenu.style.display = "none";
    }
  });
  window.onload = function () {
    var ww = window.innerWidth;
  };
  window.onresize = function () {
    var ww = window.innerWidth;
    LeMenu.style.display = ww > 530 ? "" : "none";
  };
});
