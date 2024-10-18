var date = new Date();
var mois = date.getMonth();
var annee = date.getFullYear();

const ERROR_DELAI_MAX =
  "Une réservation doit être supérieur à " +
  JOUR_MIN +
  " jour" +
  (JOUR_MIN < 2 ? "" : "s");
const ERROR_DATE_RESA = "Vous ne pouvez pas choir des dates déjà réservée";

var dateDebut = null;
var dateFin = null;
var inputDateDebut = document.getElementsByName("dateDebut")[0];
var inputDateFin = document.getElementsByName("dateFin")[0];
var inputValider = document.getElementsByName("valider_calendrier")[0];

var inputNombrePersonne = document.getElementById("nb_personnesDevis");
var displayError = document.getElementById("error_periode");
var datesReservees = [];

const paramsGlobalURL = new URLSearchParams(document.location.search);
const id = paramsGlobalURL.get("id");

const toggleModal = (modalId, displayStyle) => {
  document.getElementById(modalId).style.display = displayStyle;
};

// document.getElementById("submit_resa").addEventListener("click", () => {
//   //Enregistrer un devis en bdd
//   //toggleModal("myModal_cvg", "flex");
// });

document.querySelector(".close").addEventListener("click", () => {
  toggleModal("myModal_cvg", "none");
  toggleModal("date_resa", "none");
});

document.getElementById("declineButton").addEventListener("click", () => {
  toggleModal("myModal_cvg", "none");
});

const verifyValue = (event) => {
  const key = event.key || event.target.value;

  if (!/^\d+$/.test(key) && key !== "Backspace") {
    event.preventDefault();
  }

  if (event.target.value > NB_VOY) {
    event.target.value = NB_VOY;
  } else if (event.target.value == 0) {
    event.target.value = "";
  }
};

inputNombrePersonne.addEventListener("keydown", verifyValue);
inputNombrePersonne.addEventListener("input", verifyValue);

const verifyAndFetch = () => {
  if (
    "" != inputDateDebut.value.trim() &&
    "" != inputDateFin.value.trim() &&
    "" != inputNombrePersonne.value.trim()
  ) {
    const params = new URLSearchParams();
    params.append("dateDebut", inputDateDebut.value);
    params.append("dateFin", inputDateFin.value);
    params.append("nombrePersonne", inputNombrePersonne.value);
    params.append("id", id);

    xhr.open("GET", "../ajax/afficher-prix.ajax.php?" + params, true);
    //xhr.open("GET", "http://localhost/MKWEB/MKWeb/html/ajax/afficher-prix.ajax.php?" + params, true);

    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        document.getElementById("appear_calcul").style.display = "block";
        var responseData = JSON.parse(xhr.responseText);

        document.getElementById("prix__total_des_nuits").innerHTML = responseData.prix_ttc;
        document.getElementsByName("prix_ht")[0].value = responseData.prix_ht;

        document.getElementById("base_tarif_pour_nuit").innerHTML =
          responseData.base_tarif;

        document.getElementById("nb_jours").innerHTML =
          responseData.nombre_nuit;
        document.getElementsByName("nb_jours")[0].value =
          responseData.nombre_nuit;

        document.getElementsByName("nb_nuit")[0].value =
          responseData.nombre_nuit;

        document.getElementById("frais__total").innerHTML = responseData.frais;
        document.getElementsByName("frais")[0].value = responseData.frais;

        document.getElementById("taxes__total").innerHTML = responseData.taxe;
        document.getElementsByName("taxe")[0].value = responseData.taxe;

        document.getElementById("tot-ttc").innerHTML = responseData.prix_total_ttc;
        document.getElementsByName("prix_ttc")[0].value = responseData.prix_total_ttc;
      }
    };

    xhr.send();
  } else {
    document.getElementById("appear_calcul").style.display = "none";
  }
};

const afficherCalendrier = (mois, annee) => {
  var joursNoms = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
  var moisNoms = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Août",
    "Septembre",
    "Octobre",
    "Novembre",
    "Décembre",
  ];

  var table = document.getElementById("calendar");
  while (table.rows.length > 0) table.deleteRow(0);
  var ligne = table.insertRow();

  // Ajouter les jours de la semaine
  for (var i = 0; i < joursNoms.length; i++) {
    var cellule = ligne.insertCell();
    cellule.innerHTML = joursNoms[i];
    cellule.classList.add("style_calendar");
  }

  var premierJourDuMois = new Date(annee, mois, 1).getDay();
  var nombreDeJoursDansMois = new Date(annee, mois + 1, 0).getDate();
  ligne = table.insertRow();

  // Ajouter les espaces pour les jours avant le premier jour du mois
  for (var i = 0; i < premierJourDuMois; i++) {
    ligne.insertCell();
  }

  // Remplir les jours du mois
  var jourCourant = 1;
  for (var i = premierJourDuMois; i < 7; i++) {
    var cellule = ligne.insertCell();
    cellule.innerHTML = jourCourant++;
    var dateSelectionnee = new Date(annee, mois, jourCourant - 1);
    if (estReservee(dateSelectionnee)) {
      cellule.classList.add("reserved");
    } else {
      cellule.addEventListener("click", function () {
        var jour = parseInt(this.innerHTML);
        var dateSelectionnee = new Date(annee, mois, jour);

        if (!dateDebut || (dateFin && dateSelectionnee < dateDebut)) {
          dateDebut = dateSelectionnee;
          dateFin = null;
          inputDateDebut.value = formatDate(dateDebut);
          inputDateFin.value = "";
        } else if (dateDebut.getTime() == dateSelectionnee.getTime()) {
          dateDebut = null;
          if (dateFin != null) {
            dateDebut = dateFin;
            dateFin = null;
            inputDateFin.value = "";
            inputDateDebut.value = formatDate(dateDebut);
          } else {
            inputDateDebut.value = "";
            inputDateFin.value = "";
          }

          updateCalendar();
        } else if (
          dateFin != null &&
          dateFin.getTime() == dateSelectionnee.getTime()
        ) {
          dateFin = null;
          inputDateFin.value = "";
        } else if (!dateFin || dateSelectionnee > dateDebut) {
          dateFin = dateSelectionnee;
          inputDateFin.value = formatDate(dateFin);
          if (dateFin < dateDebut) {
            var temp = dateDebut;
            dateDebut = dateFin;
            dateFin = temp;
            inputDateDebut.value = formatDate(dateDebut);
            inputDateFin.value = formatDate(dateFin);
            updateCalendar();
          }
          // Vérifier si la période est supérieur à 4 jours
          var diffDays =
            Math.round((dateFin - dateDebut) / (1000 * 60 * 60 * 24)) + 1;
          if (diffDays <= JOUR_MIN) {
            displayError.style.display = "block";
            displayError.innerHTML = ERROR_DELAI_MAX;
            setTimeout(() => {
              displayError.style.display = "none";
            }, 3000);

            dateDebut = "";
            dateFin = "";
            inputDateFin.value = "";
            inputDateDebut.value = "";
            updateCalendar();
          }

          // Vérifier si des dates réservées se situent entre dateDebut et dateFin
          var datesEntreDebutFin = getDatesEntreDebutFin(dateDebut, dateFin);
          if (datesEntreDebutFin.some((date) => estReservee(date))) {
            displayError.innerHTML = ERROR_DATE_RESA;
            displayError.style.display = "block";
            setTimeout(() => {
              displayError.style.display = "none";
            }, 3000);
            dateFin = null;
            dateDebut = null;
            inputDateFin.value = "";
            inputDateDebut.value = "";
            updateCalendar();
            return;
          }
        }

        updateCalendar();
        verifyAndFetch();
      });
    }
  }

  for (var semaine = 1; semaine < 6; semaine++) {
    ligne = table.insertRow();
    for (
      var jour = 0;
      jour < 7 && jourCourant <= nombreDeJoursDansMois;
      jour++
    ) {
      var cellule = ligne.insertCell();
      cellule.innerHTML = jourCourant;

      var dateSelectionnee = new Date(annee, mois, jourCourant);
      if (estReservee(dateSelectionnee)) {
        cellule.classList.add("reserved");
      } else {
        cellule.addEventListener("click", function () {
          var jour = parseInt(this.innerHTML);
          var dateSelectionnee = new Date(annee, mois, jour);

          if (!dateDebut || (dateFin && dateSelectionnee < dateDebut)) {
            dateDebut = dateSelectionnee;
            dateFin = null;
            inputDateDebut.value = formatDate(dateDebut);
            inputDateFin.value = "";
          } else if (dateDebut.getTime() == dateSelectionnee.getTime()) {
            dateDebut = null;
            if (dateFin != null) {
              dateDebut = dateFin;
              dateFin = null;
              inputDateFin.value = "";
              inputDateDebut.value = formatDate(dateDebut);
            } else {
              inputDateDebut.value = "";
              inputDateFin.value = "";
            }

            updateCalendar();
          } else if (
            dateFin != null &&
            dateFin.getTime() == dateSelectionnee.getTime()
          ) {
            dateFin = null;
            inputDateFin.value = "";
          } else if (!dateFin || dateSelectionnee > dateDebut) {
            dateFin = dateSelectionnee;
            inputDateFin.value = formatDate(dateFin);
            if (dateFin < dateDebut) {
              var temp = dateDebut;
              dateDebut = dateFin;
              dateFin = temp;
              inputDateDebut.value = formatDate(dateDebut);
              inputDateFin.value = formatDate(dateFin);
              updateCalendar();
            }

            // Vérifier si la période est supérieur à 4 jours
            var diffDays =
              Math.round((dateFin - dateDebut) / (1000 * 60 * 60 * 24)) + 1;
            if (diffDays <= JOUR_MIN) {
              displayError.style.display = "block";
              displayError.innerHTML = ERROR_DELAI_MAX;
              setTimeout(() => {
                displayError.style.display = "none";
              }, 3000);
              dateDebut = "";
              dateFin = "";
              inputDateFin.value = "";
              inputDateDebut.value = "";
              updateCalendar();
            }
            // Vérifier si des dates réservées se situent entre dateDebut et dateFin
            var datesEntreDebutFin = getDatesEntreDebutFin(dateDebut, dateFin);
            if (datesEntreDebutFin.some((date) => estReservee(date))) {
              displayError.style.display = "block";
              displayError.innerHTML = ERROR_DATE_RESA;

              setTimeout(() => {
                displayError.style.display = "none";
              }, 3000);

              dateFin = null;
              dateDebut = null;
              inputDateFin.value = "";
              inputDateDebut.value = "";
              updateCalendar();
              return;
            }
          }

          updateCalendar();
          verifyAndFetch();
        });
      }
      jourCourant++;
    }
  }

  document.getElementById("month").innerHTML = moisNoms[mois] + " " + annee;
  updateCalendar();
};

const printCalendar = () => {};

const getDatesEntreDebutFin = (dateDebut, dateFin) => {
  var dates = [];
  var currentDate = new Date(dateDebut);
  while (currentDate <= dateFin) {
    dates.push(new Date(currentDate));
    currentDate.setDate(currentDate.getDate() + 1);
  }
  return dates;
};

const formatDate = (date) => {
  var annee = date.getFullYear();
  var mois = (date.getMonth() + 1).toString().padStart(2, "0");
  var jour = date.getDate().toString().padStart(2, "0");
  return annee + "-" + mois + "-" + jour;
};

const updateCalendar = () => {
  let today = new Date();
  today.setHours(0, 0, 0, 0);

  let delaiDate = new Date(today);
  delaiDate.setDate(today.getDate() + DELAI_RES);

  let cells = document.querySelectorAll("#calendar td");
  cells.forEach(function (cell) {
    var day = parseInt(cell.innerHTML);
    var cellDate = new Date(annee, mois, day);
    cellDate.setHours(0, 0, 0, 0);

    cell.classList.remove(
      "selected",
      "selected-start",
      "selected-end",
      "passed"
    );

    if (cellDate < delaiDate) {
      cell.classList.add("passed");
      return;
    }
    if (dateDebut && cellDate.toDateString() === dateDebut.toDateString()) {
      cell.classList.add("selected-start");
    }
    if (dateFin && cellDate.toDateString() === dateFin.toDateString()) {
      cell.classList.add("selected-end");
    }
    if (dateDebut && dateFin && cellDate > dateDebut && cellDate < dateFin) {
      cell.classList.add("selected");
    }
  });
};

const moisPrecedent = () => {
  let date = new Date();
  let current_month = date.getMonth();

  // Ne pas pouvoir aller dans une date déjà passé
  if (mois == current_month) return;
  mois--;
  if (mois < 0) {
    mois = 11;
    annee--;
  }
  afficherCalendrier(mois, annee);
};

const moisSuivant = () => {
  mois++;
  if (mois > 11) {
    mois = 0;
    annee++;
  }
  afficherCalendrier(mois, annee);
};

const estReservee = (date) => {
  for (var i = 0; i < datesReservees.length; i++) {
    // Utilisation de setHours pour pas commencer une date au dessus
    var startDate = new Date(datesReservees[i].start);
    startDate.setHours(0, 0, 0, 0);
    var endDate = new Date(datesReservees[i].end);
    endDate.setHours(0, 0, 0, 0);
    if (date >= startDate && date <= endDate) {
      return true;
    }
  }
  return false;
};

// Requête AJAX

var xhr = new XMLHttpRequest();
const params = new URLSearchParams();
params.append("id", id);

xhr.open("GET", "../ajax/calendrier.ajax.php?" + params, true);
//xhr.open("GET", "http://localhost/MKWEB/MKWeb/html/ajax/calendrier.ajax.php?" + params, true);
xhr.onreadystatechange = function () {
  if (xhr.readyState == 4 && xhr.status == 200) {
    var dateRanges = JSON.parse(xhr.responseText);

    datesReservees = dateRanges.map(function (dateRange) {
      return {
        start: new Date(dateRange[0]),
        end: new Date(dateRange[1]),
      };
    });
    afficherCalendrier(mois, annee);
  }
};
xhr.send();

inputNombrePersonne.addEventListener("input", verifyAndFetch);

document.getElementById("prev").addEventListener("click", moisPrecedent);
document.getElementById("next").addEventListener("click", moisSuivant);
