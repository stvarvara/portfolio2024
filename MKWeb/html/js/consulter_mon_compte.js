document.addEventListener("DOMContentLoaded", () => {

    const compte__pays = document.getElementById('compte__pays');
    const suggestions__pays = document.getElementById("autocomplete-list-pays");
    const compte__region = document.getElementById('compte__region');
    const suggestions__region = document.getElementById("autocomplete-list-regions");
    const compte__departement = document.getElementById('compte__departement');
    const suggestions__departement = document.getElementById("autocomplete-list-departement");
    const compte__ville = document.getElementById('compte__ville');
    const suggestions__ville = document.getElementById("autocomplete-list-ville");
    const compte__code_postal = document.getElementById('compte__code_postal');

    autocompletePays();
    autocompleteRegion();
    autocompleteDepartement();
    autocompleteCommune();
    
    compte__pays.addEventListener('input', () => {
        if (compte__pays.value === "") {
            compte__pays.value = "";
            compte__pays.setAttribute("value", "");

            compte__region.value = "";
            compte__region.setAttribute("value", "");
            compte__region.setAttribute("code", "");

            compte__departement.value = "";
            compte__departement.setAttribute("value", "");
            compte__departement.setAttribute("code", "");

            compte__ville.value = "";
            compte__ville.setAttribute("value", "");
            compte__ville.setAttribute("code", "");

            compte__code_postal.value = "";
            compte__code_postal.setAttribute("value", "");
        }
    });

    compte__region.addEventListener('input', () => {
        if (compte__region.value === "") {
            compte__region.value = "";
            compte__region.setAttribute("value", "");
            compte__region.setAttribute("code", "");

            compte__departement.value = "";
            compte__departement.setAttribute("value", "");
            compte__departement.setAttribute("code", "");

            compte__ville.value = "";
            compte__ville.setAttribute("value", "");
            compte__ville.setAttribute("code", "");

            compte__code_postal.value = "";
            compte__code_postal.setAttribute("value", "");
        }
    });

    compte__departement.addEventListener('input', () => {
        if (compte__departement.value === "") {
            compte__departement.value = "";
            compte__departement.setAttribute("value", "");
            compte__departement.setAttribute("code", "");

            compte__ville.value = "";
            compte__ville.setAttribute("value", "");
            compte__ville.setAttribute("code", "");

            compte__code_postal.value = "";
            compte__code_postal.setAttribute("value", "");
        }
    });

    compte__ville.addEventListener('input', () => {
        if (compte__ville.value === "") {
            compte__ville.value = "";
            compte__ville.setAttribute("value", "");
            compte__ville.setAttribute("code", "");

            compte__code_postal.value = "";
            compte__code_postal.setAttribute("value", "");
        }
    });
    
    get_codeRegion(compte__region.value);
    get_codeDepartement(compte__departement.value);


    /* Fonctions autocomplete */
    function autocompletePays () {
        let countries = [
            "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola", "Antigua-et-Barbuda", "Arabie Saoudite", "Argentine", "Arménie", "Australie", "Autriche", "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Barbade", "Belgique", "Belize", "Bénin", "Bhoutan", "Biélorussie", "Birmanie", "Bolivie", "Bosnie-Herzégovine", "Botswana", "Brésil", "Brunei", "Bulgarie", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodge", "Cameroun", "Canada", "Chili", "Chine", "Chypre", "Colombie", "Comores", "Corée du Nord", "Corée du Sud", "Costa Rica", "Côte d'Ivoire", "Croatie", "Cuba", "Danemark", "Djibouti", "Dominique", "Égypte", "Émirats arabes unis", "Équateur", "Érythrée", "Espagne", "Eswatini", "Estonie", "États-Unis", "Éthiopie", "Fidji", "Finlande", "France", "Gabon", "Gambie", "Géorgie", "Ghana", "Grèce", "Grenade", "Guatemala", "Guinée", "Guinée-Bissau", "Guinée équatoriale", "Guyana", "Haïti", "Honduras", "Hongrie", "Îles Marshall", "Inde", "Indonésie", "Irak", "Iran", "Irlande", "Islande", "Israël", "Italie", "Jamaïque", "Japon", "Jordanie", "Kazakhstan", "Kenya", "Kirghizistan", "Kiribati", "Koweït", "Laos", "Lesotho", "Lettonie", "Liban", "Liberia", "Libye", "Liechtenstein", "Lituanie", "Luxembourg", "Macédoine du Nord", "Madagascar", "Malaisie", "Malawi", "Maldives", "Mali", "Malte", "Maroc", "Maurice", "Mauritanie", "Mexique", "Micronésie", "Moldavie", "Monaco", "Mongolie", "Monténégro", "Mozambique", "Namibie", "Nauru", "Népal", "Nicaragua", "Niger", "Nigeria", "Norvège", "Nouvelle-Zélande", "Oman", "Ouganda", "Ouzbékistan", "Pakistan", "Palaos", "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas", "Pérou", "Philippines", "Pologne", "Portugal", "Qatar", "République Centrafricaine", "République Démocratique du Congo", "République Dominicaine", "République du Congo", "République Tchèque", "Roumanie", "Royaume-Uni", "Russie", "Rwanda", "Saint-Christophe-et-Niévès", "Sainte-Lucie", "Saint-Marin", "Saint-Vincent-et-les-Grenadines", "Salomon", "Salvador", "Samoa", "Sao Tomé-et-Principe", "Sénégal", "Serbie", "Seychelles", "Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Somalie", "Soudan", "Soudan du Sud", "Sri Lanka", "Suède", "Suisse", "Suriname", "Syrie", "Tadjikistan", "Tanzanie", "Tchad", "Thaïlande", "Timor oriental", "Togo", "Tonga", "Trinité-et-Tobago", "Tunisie", "Turkménistan", "Turquie", "Tuvalu", "Ukraine", "Uruguay", "Vanuatu", "Vatican", "Venezuela", "Viêt Nam", "Yémen", "Zambie", "Zimbabwe"
        ];

        compte__pays.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            suggestions__pays.innerHTML = "";
    
            if (!query) return;
    
            const suggestions = countries.filter(country => country.toLowerCase().includes(query));
    
            suggestions.forEach(suggestion => {
                const suggestionElement = document.createElement("div");
                suggestionElement.classList.add("autocomplete-suggestion");
                suggestionElement.textContent = suggestion;
    
                suggestionElement.addEventListener("click", function () {
                    compte__pays.value = suggestion;
                    compte__pays.setAttribute("value", suggestion);
                    suggestions__pays.innerHTML = "";
                });
    
                suggestions__pays.appendChild(suggestionElement);
            });
        });

        document.addEventListener("click", function (e) {
            if (e.target !== compte__pays) {
                suggestions__pays.innerHTML = "";
            }
        });

    }

    function autocompleteRegion() {      
        compte__region.addEventListener("input", function () {
            if (compte__pays.value === "France") {
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
                                    compte__region.value = item.nom;
                                    compte__region.setAttribute("value", item.nom);
                                    compte__region.setAttribute("code", item.code);
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
            if (e.target !== compte__region) {
                suggestions__region.innerHTML = "";
            }
        });
    }

    function autocompleteDepartement() {
        compte__departement.addEventListener("input", function () {
            if (compte__pays.value === "France") {
                const query = this.value.toLowerCase();
                suggestions__departement.innerHTML = "";

                if (!query) return;

                let apiUrl = `https://geo.api.gouv.fr/departements?nom=${query}`;
                if (compte__region.value && compte__region.getAttribute("code")) {
                    apiUrl = `https://geo.api.gouv.fr/departements?codeRegion=${compte__region.getAttribute("code")}&nom=${query}`;
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
                                    compte__departement.value = item.nom;
                                    compte__departement.setAttribute("value", item.nom);
                                    compte__departement.setAttribute("code", item.code);
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
            if (e.target !== compte__departement) {
                suggestions__departement.innerHTML = "";
            }
        });
    }

    function autocompleteCommune() {
        compte__ville.addEventListener("input", function () {
            if (compte__pays.value === "France") {
                const query = this.value.toLowerCase();
                suggestions__ville.innerHTML = "";
        
                if (!query) return;

                let apiUrl = `https://geo.api.gouv.fr/communes?nom=${query}`;
        
                if (compte__region.value && compte__region.getAttribute("code")) {
                    apiUrl = `https://geo.api.gouv.fr/communes?codeRegion=${compte__region.getAttribute("code")}&nom=${query}`;
                }
                if (compte__departement.value && compte__departement.getAttribute("code")) {
                    apiUrl = `https://geo.api.gouv.fr/communes?codeDepartement=${compte__departement.getAttribute("code")}&nom=${query}`;
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
                                    compte__ville.value = item.nom;
                                    compte__ville.setAttribute("value", item.nom);
                                    compte__ville.setAttribute("code", item.code);
                                    compte__code_postal.value = item.codesPostaux.join(", ");
                                    compte__code_postal.setAttribute("value", item.code);
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
            if (e.target !== compte__ville) {
                suggestions__ville.innerHTML = "";
            }
        });
    }


    /* Fonctions get code */
    function get_codeRegion(nomRegion) {
        fetch(`https://geo.api.gouv.fr/regions?nom=${nomRegion}`)
            .then(response => response.json())
            .then(data => {
                const seen = new Set();
                data.forEach(item => {
                    if (item.nom === nomRegion) {
                        compte__region.setAttribute("code", item.code);
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function get_codeDepartement(nomDepartement) {
        fetch(`https://geo.api.gouv.fr/departements?nom=${nomDepartement}`)
            .then(response => response.json())
            .then(data => {
                const seen = new Set();
                data.forEach(item => {
                    if (item.nom === nomDepartement) {
                        compte__departement.setAttribute("code", item.code);
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    }

});