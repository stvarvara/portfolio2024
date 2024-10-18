"use strict";
window.addEventListener("DOMContentLoaded", (event) => {
  // menu general
  const LeMenu = document.getElementById("LeMenu");


  const btnDisconnect = document.getElementById("disconnect");

  // menu general


  window.onload = function () {
    var ww = window.innerWidth;
    LeMenu.style.display = ww > 530 ? "" : "none";
  };
  window.onresize = function () {
    var ww = window.innerWidth;
    LeMenu.style.display = ww > 530 ? "" : "none";
  };
});
