let slideIndex = 0;
let slides = document.getElementsByClassName("slide");
let slideInterval;

function showSlides(n) {
    if (n >= slides.length) { slideIndex = 0 }
    if (n < 0) { slideIndex = slides.length - 1 }
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slides[slideIndex].style.display = "block";
}

function nextSlide() {
    showSlides(++slideIndex);
}

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function startSlideShow() {
    slideInterval = setInterval(nextSlide, 5000); 
}

function stopSlideShow() {
    clearInterval(slideInterval);
}

document.addEventListener("DOMContentLoaded", function() {

    showSlides(slideIndex);
    startSlideShow();
    
    document.querySelector('.prev').addEventListener('click', function() {
        stopSlideShow();
        plusSlides(-1);
        startSlideShow();
    });

    document.querySelector('.next').addEventListener('click', function() {
        stopSlideShow();
        plusSlides(1);
        startSlideShow();
    });


    const connect__pays = document.getElementById('connect__pays');
    const suggestions__pays = document.getElementById("autocomplete-list-pays");
    const connect__region = document.getElementById('connect__region');
    const suggestions__region = document.getElementById("autocomplete-list-region");
    const connect__departement = document.getElementById('connect__departement');
    const suggestions__departement = document.getElementById("autocomplete-list-departement");
    const connect__ville = document.getElementById('connect__ville');
    const suggestions__ville = document.getElementById("autocomplete-list-ville");
    const connect__code = document.getElementById('connect__code');

    autocompletePays();
    autocompleteRegion();
    autocompleteDepartement();
    autocompleteCommune();
    
    connect__pays.addEventListener('input', () => {
        if (connect__pays.value === "") {
            connect__pays.value = "";
            connect__pays.setAttribute("value", "");

            connect__region.value = "";
            connect__region.setAttribute("value", "");
            connect__region.setAttribute("code", "");

            connect__departement.value = "";
            connect__departement.setAttribute("value", "");
            connect__departement.setAttribute("code", "");

            connect__ville.value = "";
            connect__ville.setAttribute("value", "");
            connect__ville.setAttribute("code", "");

            connect__code.value = "";
            connect__code.setAttribute("value", "");
        }
    });

    connect__region.addEventListener('input', () => {
        if (connect__region.value === "") {
            connect__region.value = "";
            connect__region.setAttribute("value", "");
            connect__region.setAttribute("code", "");

            connect__departement.value = "";
            connect__departement.setAttribute("value", "");
            connect__departement.setAttribute("code", "");

            connect__ville.value = "";
            connect__ville.setAttribute("value", "");
            connect__ville.setAttribute("code", "");

            connect__code.value = "";
            connect__code.setAttribute("value", "");
        }
    });

    connect__departement.addEventListener('input', () => {
        if (connect__departement.value === "") {
            connect__departement.value = "";
            connect__departement.setAttribute("value", "");
            connect__departement.setAttribute("code", "");

            connect__ville.value = "";
            connect__ville.setAttribute("value", "");
            connect__ville.setAttribute("code", "");

            connect__code.value = "";
            connect__code.setAttribute("value", "");
        }
    });

    connect__ville.addEventListener('input', () => {
        if (connect__ville.value === "") {
            connect__ville.value = "";
            connect__ville.setAttribute("value", "");
            connect__ville.setAttribute("code", "");

            connect__code.value = "";
            connect__code.setAttribute("value", "");
        }
    });

    /* Fonctions autocomplete */
    function autocompletePays () {
        let countries = [
            "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola", "Antigua-et-Barbuda", "Arabie Saoudite", "Argentine", "Arménie", "Australie", "Autriche", "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Barbade", "Belgique", "Belize", "Bénin", "Bhoutan", "Biélorussie", "Birmanie", "Bolivie", "Bosnie-Herzégovine", "Botswana", "Brésil", "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodge", "Cameroun", "Canada", "Chili", "Chine", "Chypre", "Colombie", "Comores", "Corée du Nord", "Corée du Sud", "Costa Rica", "Côte d'Ivoire", "Croatie", "Cuba", "Danemark", "Djibouti", "Dominique", "Égypte", "Émirats arabes unis", "Équateur", "Érythrée", "Espagne", "Eswatini", "Estonie", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France", "Gabon", "Gambie", "Géorgie", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée", "Guinée-Bissau", "Guinée équatoriale", "Guyana", "Haïti", "Honduras", "Hongrie", "Îles Marshall", "Inde", "Indonésie", "Irak", "Iran", "Irlande", "Islande", "Israël", "Italie", "Jamaïque", "Japon", "Jordanie", "Kazakhstan", "Kenya", "Kirghizistan", "Kiribati", "Koweït", "Laos", "Lesotho", "Lettonie", "Liban", "Liberia", "Libye", "Liechtenstein", "Lituanie", "Luxembourg", "Macédoine du Nord", "Madagascar", "Malaisie", "Malawi", "Maldives", "Mali", "Malte", "Maroc", "Maurice", "Mauritanie", "Mexique", "Micronésie", "Moldavie", "Monaco", "Mongolie", "Monténégro", "Mozambique", "Namibie", "Nauru", "Népal", "Nicaragua", "Niger", "Nigeria", "Norvège", "Nouvelle-Zélande", "Oman", "Ouganda", "Ouzbékistan", "Pakistan", "Palaos", "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas", "Pérou", "Philippines", "Pologne", "Portugal", "Qatar", "République Centrafricaine", "République Démocratique du Congo", "République Dominicaine", "République du Congo", "République Tchèque", "Roumanie", "Royaume-Uni", "Russie", "Rwanda", "Saint-Christophe-et-Niévès", "Sainte-Lucie", "Saint-Marin", "Saint-Vincent-et-les-Grenadines", "Salomon", "Salvador", "Samoa", "Sao Tomé-et-Principe", "Sénégal", "Serbie", "Seychelles", "Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Somalie", "Soudan", "Soudan du Sud", "Sri Lanka", "Suède", "Suisse", "Suriname", "Syrie", "Tadjikistan", "Tanzanie", "Tchad", "Thaïlande", "Timor oriental", "Togo", "Tonga", "Trinité-et-Tobago", "Tunisie", "Turkménistan", "Turquie", "Tuvalu", "Ukraine", "Uruguay", "Vanuatu", "Vatican", "Venezuela", "Viêt Nam", "Yémen", "Zambie", "Zimbabwe"
        ];

        connect__pays.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            suggestions__pays.innerHTML = "";
    
            if (!query) return;
    
            const suggestions = countries.filter(country => country.toLowerCase().includes(query));
    
            suggestions.forEach(suggestion => {
                const suggestionElement = document.createElement("div");
                suggestionElement.classList.add("autocomplete-suggestion");
                suggestionElement.textContent = suggestion;
    
                suggestionElement.addEventListener("click", function () {
                    connect__pays.value = suggestion;
                    connect__pays.setAttribute("value", suggestion);
                    suggestions__pays.innerHTML = "";
                });
    
                suggestions__pays.appendChild(suggestionElement);
            });
        });

        document.addEventListener("click", function (e) {
            if (e.target !== connect__pays) {
                suggestions__pays.innerHTML = "";
            }
        });

    }

    function autocompleteRegion() {      
        connect__region.addEventListener("input", function () {
            if (connect__pays.value === "France") {
                const query = this.value.toLowerCase();
                suggestions__region.innerHTML = "";

                if (!query) return;

                fetch(`https://geo.api.gouv.fr/regions?nom=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        const seen = new Set();
                        data.forEach(item => {
                            if (!seen.has(item.nom)) { 
                                seen.add(item.nom); 
                                const suggestionElement = document.createElement("div");
                                suggestionElement.classList.add("autocomplete-suggestion");
                                suggestionElement.textContent = item.nom;

                                suggestionElement.addEventListener("click", function () {
                                    connect__region.value = item.nom;
                                    connect__region.setAttribute("value", item.nom);
                                    connect__region.setAttribute("code", item.code);
                                    suggestions__region.innerHTML = "";
                                });

                                suggestions__region.appendChild(suggestionElement);
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }
        });
    
        document.addEventListener("click", function (e) {
            if (e.target !== connect__region) {
                suggestions__region.innerHTML = "";
            }
        });
    }

    function autocompleteDepartement() {
        connect__departement.addEventListener("input", function () {
            if (connect__pays.value === "France") {
                const query = this.value.toLowerCase();
                suggestions__departement.innerHTML = "";

                if (!query) return;

                let apiUrl = `https://geo.api.gouv.fr/departements?nom=${query}`;
                if (connect__region.value && connect__region.getAttribute("code")) {
                    apiUrl = `https://geo.api.gouv.fr/departements?codeRegion=${connect__region.getAttribute("code")}&nom=${query}`;
                }

                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        const seen = new Set();
                        data.forEach(item => {
                            if (!seen.has(item.nom)) {
                                seen.add(item.nom);
                                const suggestionElement = document.createElement("div");
                                suggestionElement.classList.add("autocomplete-suggestion");
                                suggestionElement.textContent = item.nom + " (" + item.code + ")";

                                suggestionElement.addEventListener("click", function () {
                                    connect__departement.value = item.nom;
                                    connect__departement.setAttribute("value", item.nom);
                                    connect__departement.setAttribute("code", item.code);
                                    suggestions__departement.innerHTML = "";
                                });

                                suggestions__departement.appendChild(suggestionElement);
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }
        });
    
        document.addEventListener("click", function (e) {
            if (e.target !== connect__departement) {
                suggestions__departement.innerHTML = "";
            }
        });
    }

    function autocompleteCommune() {
        connect__ville.addEventListener("input", function () {
            if (connect__pays.value === "France") {
                const query = this.value.toLowerCase();
                suggestions__ville.innerHTML = "";
        
                if (!query) return;

                let apiUrl = `https://geo.api.gouv.fr/communes?nom=${query}`;
        
                if (connect__region.value && connect__region.getAttribute("code")) {
                    apiUrl = `https://geo.api.gouv.fr/communes?codeRegion=${connect__region.getAttribute("code")}&nom=${query}`;
                }
                if (connect__departement.value && connect__departement.getAttribute("code")) {
                    apiUrl = `https://geo.api.gouv.fr/communes?codeDepartement=${connect__departement.getAttribute("code")}&nom=${query}`;
                }
        
                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        const seen = new Set();
                        data.forEach(item => {
                            if (!seen.has(item.nom)) {
                                seen.add(item.nom);
                                const suggestionElement = document.createElement("div");
                                suggestionElement.classList.add("autocomplete-suggestion");
                                suggestionElement.textContent = item.nom + " (" + item.codesPostaux.join(", ") + ")";

                                suggestionElement.addEventListener("click", function () {
                                    connect__ville.value = item.nom;
                                    connect__ville.setAttribute("value", item.nom);
                                    connect__ville.setAttribute("code", item.code);
                                    connect__code.value = item.codesPostaux.join(", ");
                                    connect__code.setAttribute("value", item.code);
                                    suggestions__ville.innerHTML = "";
                                });

                                suggestions__ville.appendChild(suggestionElement);
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }
        });
    
        document.addEventListener("click", function (e) {
            if (e.target !== connect__ville) {
                suggestions__ville.innerHTML = "";
            }
        });
    }

});

