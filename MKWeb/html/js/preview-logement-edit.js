
const enregistrerButton = document.getElementById("enregistrer");

const loadingModal = document.querySelector(".loading__modal");

var isSubmiting = false;

const updateSubmitingButton = () => {
  console.log("toggle show");
  loadingModal.style.display = isSubmiting ? "flex" : "none";
};

enregistrerButton.addEventListener("click", (e) => {
  e.preventDefault();
  isSubmiting = true;
  updateSubmitingButton();
  fetch("../ajax/modifier-logement.ajax.php", {
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      if (data.err == undefined || data.err == false) {
        isSubmiting = false;
        updateSubmitingButton();
        sessionStorage.setItem('message', 'Logement modifié avec succès!');
        window.location.href = "liste-logement.php";
      } else {
        window.location.reload();
        isSubmiting = false;
        updateSubmitingButton();
      }
    });
});
