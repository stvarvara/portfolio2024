function togglePasswordVisibility() {
  var passwordInput = document.getElementById("compte__mdp");
  var checkbox = document.getElementById("showPassword");

  if (checkbox.checked) {
    passwordInput.type = "text";
  } else {
    passwordInput.type = "password";
  }
}
/**
 * API
 */
document.getElementById("selectAllCheckbox-api").onclick = function () {
  document.querySelectorAll(".logementCheckbox-api").forEach((checkbox) => {
    checkbox.checked = document.getElementById("selectAllCheckbox-api").checked;
  });
};

var api = document.querySelectorAll(".api_id");
var copier_api = document.querySelectorAll(".copier-api");
var eyes_api = document.querySelectorAll(".eyes-api");
var cross_api = document.querySelectorAll(".cross-api");
var supp_api = document.getElementById("confirmBtn");
var modifiers_api = document.querySelectorAll(".modifier-api");
var tokenToDelete = "";
var apiToDelete = "";
var gen_token = document.getElementById("generer_token");
var gen_api = document.getElementById("generer_api");

var modalEnregApi = document.getElementById("modal_enreg-api");
var closeModalApi = document.getElementById("closeModalBtn-api");
var annulerBtnApi = document.getElementById("closeBtn-api");
var droitsContainer = document.getElementById("droitsContainer");

var token = document.querySelectorAll(".token_id");
var text_content = document.getElementById("text-content");
var cross = document.querySelectorAll(".cross");
var copier = document.querySelectorAll(".copier");
var eyes = document.querySelectorAll(".eyes");
var modifiers = document.querySelectorAll(".modifier");
var logementCheckboxes = document.querySelectorAll(".logementCheckbox");
var modalEnreg = document.getElementById("modal_enreg");
var annulerBtn = document.getElementById("closeBtn");
var closeModalBtn = document.getElementById("closeModalBtn");

var alreadyIn = document.getElementById("alreadyIn");

var date_fin = document.getElementById("date_fin");
var date_debut = document.getElementById("date_debut");
var action = document.getElementById("action");
var actionApi = document.getElementById("action-api");
let name= "";
closeModalApi.onclick = function () {
    modalEnregApi.style.display = "none";
    document.body.classList.remove("no-scroll");
    //logementsContainer.innerHTML = ''
    document.getElementById("api-key").value = "";
    //alreadyIn.value = "";
    document.body.style.overflow = "visible";
    document.getElementById("droitsContainer").innerHTML = "";

};
annulerBtnApi.onclick = function () {
  modalEnregApi.style.display = "none";
  document.body.classList.remove("no-scroll");
  //logementsContainer.innerHTML = ''
  document.getElementById("api-key").value = "";
  //alreadyIn.value = "";
  document.body.style.overflow = "visible";
  document.getElementById("droitsContainer").innerHTML = "";
};

eyes_api.forEach((v, k) => {
  v.addEventListener("click", () => {
    if (api[k].type === "password") {
      api[k].type = "text";
      v.firstElementChild.classList = "";
      v.firstElementChild.classList.add("fas", "fa-eye-slash", "eye-icon");
    } else {
      api[k].type = "password";
      v.firstElementChild.classList = "";
      v.firstElementChild.classList.add("fas", "fa-eye");
    }
  });
});
copier_api.forEach((v, k) => {
  v.addEventListener("click", () => {
    let url = api[k].value;
    navigator.clipboard.writeText(url);
    v.firstElementChild.classList = "";
    v.firstElementChild.classList.add("fas", "fa-check-double", "copied-icon");
    showToast("Clé copié");
    setTimeout(() => {
      v.firstElementChild.classList.add("fas", "fa-copy");
    }, 2000);
  });
});

cross_api.forEach((v, k) => {
  v.addEventListener("click", () => {
    apiToDelete = api[k].value;
    showModal();
    text_content.innerHTML = `Êtes vous sur de vouloir supprimer la clé ${api[k].value} ?`;
  });
});
// Attacher un gestionnaire d'événements unique au bouton 'confirmBtn'
confirmBtn.addEventListener("click", () => {
  if (apiToDelete !== "" && tokenToDelete == "") {
    const xhr = new XMLHttpRequest();
    const params = new URLSearchParams();
    params.append("action", "delete");
    params.append("api", apiToDelete);

    xhr.open("GET", "../ajax/api.ajax.php?" + params, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        console.log(xhr.responseText);

        hideModal(); // Cacher la modal après la suppression
        apiToDelete = ""; // Réinitialiser le token à supprimer
        location.reload();
      }
    };
    xhr.send();
  }
});

modifiers_api.forEach((modifier, k) => {
  droitsContainer.innerHTML = "";
  modifier.addEventListener("click", () => {
    actionApi.value = "update";
    document.body.style.overflow = "hidden";

    modalEnregApi.style.display = "block";
    document.body.classList.add("no-scroll");
    // Requête AJAX

    var xhr = new XMLHttpRequest();
    const params = new URLSearchParams();
    params.append("action", "update");
    document.getElementById("api-key").value = api[k].value;

    params.append("api", api[k].value);
    //console.log(token[k].value);

    xhr.open("GET", "../ajax/api.ajax.php?" + params, true);
    //xhr.open("GET", "http://localhost/MKWEB/MKWeb/html/ajax/calendrier.ajax.php?" + params, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        //console.log(xhr.responseText);
        permissions = JSON.parse(xhr.responseText);
        const droitsContainer = document.getElementById("droitsContainer");
        //console.log(data);
        
        for (const key in permissions) {
        
          if (key !== "admin") {
            if(key === 'indispo')name = "Modifier la disponibilitée du logement";
            if(key === 'planning')name="Voir disponibilitée du logement";
            if(key==='lister')name= "Voir la liste de mes logements";
            
            const logementDiv = document.createElement("div");
            logementDiv.classList.add("logement");

            const checkboxDiv = document.createElement("div");
            checkboxDiv.classList.add("checkbox");
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.value += "/" + key;

            checkbox.name = "check_logement[]";
            checkbox.checked = permissions[key] == 1 ? "checked" : "";
            const p = document.createElement("p");
            p.textContent = name;
            const descriptionDiv = document.createElement("div");
            descriptionDiv.appendChild(p);
            checkbox.classList.add("logementCheckbox-api");
            checkboxDiv.appendChild(checkbox);
            logementDiv.appendChild(checkboxDiv);
            logementDiv.appendChild(descriptionDiv);
            droitsContainer.appendChild(logementDiv);
          }

          //console.log(`${key}: ${permissions[key]}`);
        }
      }
    };
    xhr.send();
  });
});

gen_api.addEventListener("click", () => {
  actionApi.value = "create";
  document.body.style.overflow = "hidden";

  modalEnregApi.style.display = "block";
  var xhr = new XMLHttpRequest();
  const params = new URLSearchParams();
  params.append("action", "create");
  params.append("id", BUSINESS_ID);

  xhr.open("GET", "../ajax/api.ajax.php?" + params, true);
  //xhr.open("GET", "http://localhost/MKWEB/MKWeb/html/ajax/calendrier.ajax.php?" + params, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      console.log(xhr.responseText);
      var data = JSON.parse(xhr.responseText);
      permissions = JSON.parse(xhr.responseText);
      const droitsContainer = document.getElementById("droitsContainer");
      //console.log(data);

      for (const key in permissions) {
        if (key !== "admin") {
          if(key === 'indispo')name = "Modifier la disponibilitée du logement";
            if(key === 'planning')name="Voir disponibilitée du logement";
            if(key==='lister')name= "Voir la liste de mes logements";
          const logementDiv = document.createElement("div");
          logementDiv.classList.add("logement");

          const checkboxDiv = document.createElement("div");
          checkboxDiv.classList.add("checkbox");
          const checkbox = document.createElement("input");
          checkbox.type = "checkbox";
          checkbox.value += "/" + key;

          checkbox.name = "check_logement[]";
          checkbox.checked = permissions[key] == 1 ? "checked" : "";
          const p = document.createElement("p");
          p.textContent = name;
          const descriptionDiv = document.createElement("div");
          descriptionDiv.appendChild(p);
          checkbox.classList.add("logementCheckbox-api");
          checkboxDiv.appendChild(checkbox);
          logementDiv.appendChild(checkboxDiv);
          logementDiv.appendChild(descriptionDiv);
          droitsContainer.appendChild(logementDiv);
        }

        //console.log(`${key}: ${permissions[key]}`);
      }
    }
  };
  xhr.send();
});

/**
 * TOKEN
 */

date_fin.addEventListener("change", function (e) {
  if (new Date(date_fin.value) < new Date(date_debut.value)) {
    alert("La date de fin doit être supérieur à celle du début.");
    date_fin.value = "";
  }
});

date_debut.addEventListener("change", function (e) {
  if (new Date(date_fin.value) < new Date(date_debut.value)) {
    alert("La date de fin doit être supérieur à celle du début.");
    date_debut.value = "";
  }
});

gen_token.addEventListener("click", () => {
  document.body.style.overflow = "hidden";
  action.value = "create";
  modalEnreg.style.display = "block";
  var xhr = new XMLHttpRequest();
  const params = new URLSearchParams();
  params.append("action", "create");
  params.append("id", BUSINESS_ID);

  xhr.open("GET", "../ajax/ical.ajax.php?" + params, true);
  //xhr.open("GET", "http://localhost/MKWEB/MKWeb/html/ajax/calendrier.ajax.php?" + params, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      var data = JSON.parse(xhr.responseText);
      for (logement of data["logement"]) {
        //console.log(logement);
        //console.log(logement['titre']);
        
        const logementDiv = document.createElement("div");
        logementDiv.classList.add("logement");

        const checkboxDiv = document.createElement("div");
        checkboxDiv.classList.add("checkbox");
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.value = logement["id_logement"];
        checkbox.name = "check_logement[]";

        checkbox.classList.add("logementCheckbox");
        checkboxDiv.appendChild(checkbox);

        const descriptionDiv = document.createElement("div");
        descriptionDiv.classList.add("description");
        const img = document.createElement("img");
        img.src = "../img" + logement["img"];
        img.alt = logement["titre"];
        const p = document.createElement("p");
        p.textContent = logement["titre"];
        descriptionDiv.appendChild(img);
        descriptionDiv.appendChild(p);
        logementDiv.appendChild(checkboxDiv);
        logementDiv.appendChild(descriptionDiv);

        logementsContainer.appendChild(logementDiv);
      }
    }
  };
  xhr.send();
});
closeModalBtn.onclick = function () {
  modalEnreg.style.display = "none";
  document.body.classList.remove("no-scroll");
  logementsContainer.innerHTML = "";
  document.getElementById("token").value = "";
  alreadyIn.value = "";
  document.body.style.overflow = "visible";
};
annulerBtn.onclick = function () {
  modalEnreg.style.display = "none";
  document.body.classList.remove("no-scroll");
  logementsContainer.innerHTML = "";
  document.getElementById("token").value = "";
  alreadyIn.value = "";
  document.body.style.overflow = "visible";
};

modifiers.forEach((modifier, k) => {
  modifier.addEventListener("click", () => {
    action.value = "update";
    document.body.style.overflow = "hidden";

    modalEnreg.style.display = "block";
    document.body.classList.add("no-scroll");
    // Requête AJAX

    var xhr = new XMLHttpRequest();
    const params = new URLSearchParams();
    params.append("action", "update");
    document.getElementById("token").value = token[k].value;

    params.append("token", token[k].value);
    //console.log(token[k].value);

    xhr.open("GET", "../ajax/ical.ajax.php?" + params, true);
    //xhr.open("GET", "http://localhost/MKWEB/MKWeb/html/ajax/calendrier.ajax.php?" + params, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        //console.log(xhr.responseText);
        var data = JSON.parse(xhr.responseText);
        date_fin.value = data.date_fin;
        date_debut.value = data.date_debut;

        const logementsContainer =
          document.getElementById("logementsContainer");
        //console.log(data['logement']);
        for (logement of data["logement"]) {
          //console.log(logement['titre']);
          const logementDiv = document.createElement("div");
          logementDiv.classList.add("logement");

          const checkboxDiv = document.createElement("div");
          checkboxDiv.classList.add("checkbox");
          const checkbox = document.createElement("input");
          checkbox.type = "checkbox";
          checkbox.value = logement["id_logement"];
          alreadyIn.value =
            alreadyIn.value == ""
              ? logement["id_logement"]
              : alreadyIn.value + "/" + logement["id_logement"];
          checkbox.name = "check_logement[]";
          checkbox.checked = "checked";
          checkbox.classList.add("logementCheckbox");
          checkboxDiv.appendChild(checkbox);

          const descriptionDiv = document.createElement("div");
          descriptionDiv.classList.add("description");
          const img = document.createElement("img");
          img.src = "../img" + logement["img"];
          img.alt = logement["titre"];
          const p = document.createElement("p");
          p.textContent = logement["titre"];
          descriptionDiv.appendChild(img);
          descriptionDiv.appendChild(p);
          logementDiv.appendChild(checkboxDiv);
          logementDiv.appendChild(descriptionDiv);

          logementsContainer.appendChild(logementDiv);
        }
        val_already = alreadyIn.value.split("/");
        for (logement of data["all_logement"]) {
          if (!val_already.includes(logement["id_logement"].toString())) {
            //console.log(logement['titre']);
            const logementDiv = document.createElement("div");
            logementDiv.classList.add("logement");

            const checkboxDiv = document.createElement("div");
            checkboxDiv.classList.add("checkbox");
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.value = logement["id_logement"];
            checkbox.name = "check_logement[]";

            checkbox.classList.add("logementCheckbox");
            checkboxDiv.appendChild(checkbox);

            const descriptionDiv = document.createElement("div");
            descriptionDiv.classList.add("description");
            const img = document.createElement("img");
            img.src = "../img" + logement["img"];
            img.alt = logement["titre"];
            const p = document.createElement("p");
            p.textContent = logement["titre"];
            descriptionDiv.appendChild(img);
            descriptionDiv.appendChild(p);
            logementDiv.appendChild(checkboxDiv);
            logementDiv.appendChild(descriptionDiv);

            logementsContainer.appendChild(logementDiv);
          }
        }
      }
    };
    xhr.send();
  });
});

eyes.forEach((v, k) => {
  v.addEventListener("click", () => {
    if (token[k].type === "password") {
      token[k].type = "text";
      v.firstElementChild.classList = "";
      v.firstElementChild.classList.add("fas", "fa-eye-slash", "eye-icon");
    } else {
      token[k].type = "password";
      v.firstElementChild.classList = "";
      v.firstElementChild.classList.add("fas", "fa-eye");
    }
  });
});
copier.forEach((v, k) => {
  v.addEventListener("click", () => {
    let url = SERVER_NAME + token[k].value;
    navigator.clipboard.writeText(url);

    v.firstElementChild.classList = "";
    v.firstElementChild.classList.add("fas", "fa-check-double", "copied-icon");
    showToast("Lien copié");
    setTimeout(() => {
      v.firstElementChild.classList.add("fas", "fa-copy");
    }, 2000);
  });
});

cross.forEach((v, k) => {
  v.addEventListener("click", () => {
    tokenToDelete = token[k].value;
    showModal();
    text_content.innerHTML = `Êtes vous sur de vouloir supprimer le token ${token[k].value} ?
                                  En cas de suppression les calendriers utilisant ce token ne fonctionnerons plus.`;
  });
});
// Attacher un gestionnaire d'événements unique au boutDELETEon 'confirmBtn'
confirmBtn.addEventListener("click", () => {
  if (tokenToDelete !== "" && apiToDelete == "") {
    const xhr = new XMLHttpRequest();
    const params = new URLSearchParams();
    params.append("action", "delete");
    params.append("token", tokenToDelete);

    xhr.open("GET", "../ajax/ical.ajax.php?" + params, true);
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        //console.log(xhr.responseText);

        hideModal(); // Cacher la modal après la suppression
        tokenToDelete = ""; // Réinitialiser le token à supprimer
        location.reload();
      }
    };
    xhr.send();
  }
});
selectAllCheckbox.onclick = function () {
  document.querySelectorAll(".logementCheckbox").forEach((checkbox) => {
    checkbox.checked = selectAllCheckbox.checked;
  });
};

const showModal = () => {
  document.getElementById("modal").classList.add("show");
  document.body.style.overflow = "hidden";
};

const hideModal = () => {
  document.getElementById("modal").classList.remove("show");
  document.body.style.overflow = "visible";
};

document.getElementById("confirmBtn").addEventListener("click", function () {
  hideModal();
});

document.getElementById("cancelBtn").addEventListener("click", function () {
  hideModal();
});


