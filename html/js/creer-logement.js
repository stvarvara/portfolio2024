const previewDiv = document.getElementById("image-preview");
const imageInput = document.getElementById("image-input");
const logementForm = document.getElementById("nv-logement");

const btnAddAmenagement = document.getElementById("ajouter__amenagement");
const divAmenagement = document.getElementById("list__amenagement");
const nameAmenagement = document.getElementById("name__amenagement");
const distanceAmenagement = document.getElementById("distance__amenagement");

const inputCheckbox = document.querySelectorAll(".input__checkbox");

inputCheckbox.forEach((check) => {
  check.addEventListener("click", (e) => {
    if (e.target.matches("input")) return;
    let checkbox = e.currentTarget.querySelector("input");
    checkbox.checked = !checkbox.checked;
  });
});

var imageList = [];

btnAddAmenagement.addEventListener("click", () => {
  if (nameAmenagement.value.length < 3) {
    nameAmenagement.focus();
    return;
  }

  let amenagement = {
    name: nameAmenagement.value,
    distanceID: distanceAmenagement[distanceAmenagement.selectedIndex].value,
    distance:
      distanceAmenagement[distanceAmenagement.selectedIndex].textContent,
  };

  let div = document.createElement("div");
  let btnDel = document.createElement("button");
  let p = document.createElement("p");
  let span = document.createElement("span");
  let input = document.createElement("input");

  btnDel.type = "button";
  btnDel.classList.add("btn__remove");
  btnDel.textContent = "X";

  btnDel.addEventListener("click", () => {
    div.remove();
  });

  input.type = "hidden";
  input.name = "activite[]";
  input.value = amenagement.name + ";;" + amenagement.distanceID;

  p.textContent = amenagement.name;
  span.textContent = amenagement.distance;

  div.appendChild(p);
  div.appendChild(span);
  div.append(input);

  div.append(btnDel);

  divAmenagement.appendChild(div);
});

imageInput.addEventListener("change", (e) => {
  const files = e.target.files;
  const preview = document.getElementById("image-preview");
  var imgPreviews = Array.from(document.querySelectorAll(".img_preview"));
  imgPreviews = imgPreviews.map((m) => {
    let arr = m.src.split("/");
    return arr[arr.length - 1];
  });

  if (imgPreviews.length == 0) previewDiv.innerHTML = "";

  Array.from(files).forEach((file) => {
    if (!imgPreviews.includes(file.name)) {
      if (imageList.length > 6) {
        showToast("Maximum 5 images", true);
        return;
      }
      imageList.push(file);

      const reader = new FileReader();
      reader.onload = function (e) {
        const btnDel = document.createElement("button");
        const div = document.createElement("div");
        const img = document.createElement("img");

        img.classList.add("img_preview");

        btnDel.type = "button";
        btnDel.classList.add("btn__remove");
        btnDel.textContent = "X";

        btnDel.addEventListener("click", () => {
          div.remove();
          imageList = imageList.filter((f) => f.name != file.name);
          console.log(imageList);
        });

        img.src = e.target.result;
        div.append(img);
        div.append(btnDel);
        preview.appendChild(div);
      };
      reader.readAsDataURL(file);
    }
  });

  e.target.value = "";
});

const latitudeInput = document.getElementById("latitude");
const longitudeInput = document.getElementById("longitude");

const villeInput = document.getElementById("commune");
const regionInput = document.getElementById("region");
const departementInput = document.getElementById("departement");
const paysInput = document.getElementById("pays");
const voieInput = document.getElementById("voie");
const numVoieInput = document.getElementById("num_voie");

var isSubmiting = false;
const submitButton = document.getElementById("form__submit");
const loadingModal = document.querySelector(".loading__modal");

const previewButton = document.getElementById("form__preview");

const updateSubmitingButton = () => {
  loadingModal.style.display = isSubmiting ? "flex" : "none";
};

const prixHT = document.getElementById("prixht");
const surface = document.getElementById("surface");

submitButton.addEventListener("click", async (e) => {
  e.preventDefault();
  prixHT.value = prixHT.value.replace(/,/g, ".");
  if (/^[,.]/.test(prixHT.value)) {
    prixHT.value = prixHT.value.substring(1);
  }

  surface.value = surface.value.replace(/,/g, ".");
  if (/^[,.]/.test(surface.value)) {
    surface.value = surface.value.substring(1);
  }

  var imgPreviews = Array.from(document.querySelectorAll(".img_preview"));
  if (!logementForm.reportValidity() || isSubmiting) return;

  isSubmiting = true;
  updateSubmitingButton();

  if (imgPreviews.length > 0) {
    const data = new DataTransfer();

    imageList.forEach((img) => {
      data.items.add(img);
    });

    imageInput.files = data.files;

    imageInput.name = "images[]";
  } else {
    imageInput.focus();
    showToast("Image obligatoire", true);
    isSubmiting = false;
    updateSubmitingButton();
    return;
  }

  let adresse =
    numVoieInput.value +
    " " +
    voieInput.value +
    " " +
    villeInput.value +
    " " +
    departementInput.value +
    " " +
    regionInput.value +
    " " +
    paysInput.value;

  const url =
    "https://api.opencagedata.com/geocode/v1/json?q=" +
    encodeURIComponent(adresse) +
    "&key=90a3f846aa9e490d927a787facf78c7e";

  const response = await fetch(url);
  const data = await response.json();

  if (data.results.length > 0) {
    latitudeInput.value = data.results[0].geometry.lat;
    longitudeInput.value = data.results[0].geometry.lng;
    console.log(data);
  } else {
    console.error("Adresse invalide.");
    document.getElementById("voie").focus();
    showToast("Adresse invalide", true);
    isSubmiting = false;
    updateSubmitingButton();
    return;
  }

  paysInput.disabled = false;
  regionInput.disabled = false;

  const formData = new FormData(logementForm);

  await fetch("../ajax/store-form-data.ajax.php", {
    method: "POST",
    body: formData,
  });

  fetch("../ajax/creer-logement.ajax.php", {
    method: "POST",
  })
    .then((response) => response.text())
    .then((data) => {
      console.log(data);
      if (data.err == false) {
        isSubmiting = false;
        updateSubmitingButton();
        window.location.href = "index.php?id=" + data.id;
      } else {
        isSubmiting = false;
        updateSubmitingButton();
      }
    });
});

previewButton.addEventListener("click", async (e) => {
  e.preventDefault();
  prixHT.value = prixHT.value.replace(/,/g, ".");
  if (/^[,.]/.test(prixHT.value)) {
    prixHT.value = prixHT.value.substring(1);
  }

  surface.value = surface.value.replace(/,/g, ".");
  if (/^[,.]/.test(surface.value)) {
    surface.value = surface.value.substring(1);
  }

  var imgPreviews = Array.from(document.querySelectorAll(".img_preview"));
  if (!logementForm.reportValidity() || isSubmiting) return;

  isSubmiting = true;
  updateSubmitingButton();

  if (imgPreviews.length > 0) {
    const data = new DataTransfer();

    imageList.forEach((img) => {
      data.items.add(img);
    });

    imageInput.files = data.files;

    imageInput.name = "images[]";
  } else {
    imageInput.focus();
    showToast("Image obligatoire", true);
    isSubmiting = false;
    updateSubmitingButton();
    return;
  }

  let adresse =
    numVoieInput.value +
    " " +
    voieInput.value +
    " " +
    villeInput.value +
    " " +
    departementInput.value +
    " " +
    regionInput.value +
    " " +
    paysInput.value;

  const url =
    "https://api.opencagedata.com/geocode/v1/json?q=" +
    encodeURIComponent(adresse) +
    "&key=90a3f846aa9e490d927a787facf78c7e";

  const response = await fetch(url);
  const data = await response.json();

  if (data.results.length > 0) {
    latitudeInput.value = data.results[0].geometry.lat;
    longitudeInput.value = data.results[0].geometry.lng;
  } else {
    console.error("Adresse invalide.");
    document.getElementById("voie").focus();
    showToast("Adresse invalide", true);
    isSubmiting = false;
    updateSubmitingButton();
    return;
  }

  paysInput.disabled = false;
  regionInput.disabled = false;

  const formData = new FormData(logementForm);

  await fetch("../ajax/store-form-data.ajax.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      console.log(data);
      if (data.err == undefined || data.err == false) {
        isSubmiting = false;
        updateSubmitingButton();
        logementForm.submit();
      } else {
        isSubmiting = false;
        updateSubmitingButton();
      }
    });
});

const resetButton = document.getElementById("reset");
resetButton.addEventListener("click", async () => {
  await fetch("../ajax/reset-form-data.ajax.php", {
    method: "POST",
  });
  location.reload();
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("#image-preview .btn__remove").forEach((b) => {
    b.addEventListener("click", () => {
      b.parentElement.remove();
    });
  });
});

const verifyValueInt = (event) => {
  const key = event.key || event.target.value;

  if (
    key === "Backspace" ||
    key === "Tab" ||
    key === "ArrowLeft" ||
    key === "ArrowRight"
  ) {
    return;
  }

  if (!/^\d+$/.test(key)) {
    event.preventDefault();
  }

  const inputValue = parseInt(event.target.value);
  if (inputValue > Number.MAX_SAFE_INTEGER) {
    event.target.value = Number.MAX_SAFE_INTEGER;
  }
};

const verifyValueFloat = (event) => {
  const key = event.key;

  if (
    key === "Backspace" ||
    key === "Tab" ||
    key === "ArrowLeft" ||
    key === "ArrowRight"
  ) {
    return;
  }

  if (!/[0-9.,]/.test(key)) {
    event.preventDefault();
    return;
  }

  const inputValue = event.target.value;
  if (/.*[.,].*/.test(inputValue) && (key == "." || key == ",")) {
    event.preventDefault();
  }
  if (/^[,.]/.test(inputValue)) {
    event.target.value = inputValue.substring(1);
  }

  if (inputValue > Number.MAX_SAFE_INTEGER) {
    event.target.value = Number.MAX_SAFE_INTEGER;
  }
};

const nbPersonne = document.getElementById("nbpersonne");
const dureeLoc = document.getElementById("dureeloc");
const preavis = document.getElementById("preavis");

const chambre = document.getElementById("chambre");
const simple = document.getElementById("simple");
const double = document.getElementById("double");

prixHT.addEventListener("keydown", verifyValueFloat);
prixHT.addEventListener("input", verifyValueFloat);

nbPersonne.addEventListener("keydown", verifyValueInt);
nbPersonne.addEventListener("input", verifyValueInt);

dureeLoc.addEventListener("keydown", verifyValueInt);
dureeLoc.addEventListener("input", verifyValueInt);

preavis.addEventListener("keydown", verifyValueInt);
preavis.addEventListener("input", verifyValueInt);

surface.addEventListener("keydown", verifyValueFloat);
surface.addEventListener("input", verifyValueFloat);

chambre.addEventListener("keydown", verifyValueInt);
chambre.addEventListener("input", verifyValueInt);

simple.addEventListener("keydown", verifyValueInt);
simple.addEventListener("input", verifyValueInt);

double.addEventListener("keydown", verifyValueInt);
double.addEventListener("input", verifyValueInt);

document.addEventListener("DOMContentLoaded", function () {
  var selectElements = document.querySelectorAll("select");

  function updateSelectColor(selectElement) {
    if (selectElement.value) {
      selectElement.classList.add("selected");
    } else {
      selectElement.classList.remove("selected");
    }
  }

  selectElements.forEach(function (selectElement) {
    // Initial check
    updateSelectColor(selectElement);

    // Update color on change
    selectElement.addEventListener("change", function () {
      updateSelectColor(selectElement);
    });
  });
});
