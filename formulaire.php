<?php
session_start();




if (isset($_GET['effacer']) && $_GET['effacer'] == '1') {

    unset($_SESSION['formulaire']);  //supprimer Donnees du form de la session

    header('Location: formulaire.php');
    exit;
}




$data = $_SESSION['formulaire'] ?? [];  //recupere des donnes de session



function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}


/* 
   RÉCUPÉRER LES INFORMATIONS
    */

$modules = $data['modules'] ?? [];

if (!is_array($modules)) {
    $modules = [];
}

$annee = $data['annee'] ?? '1ère année';
$nombre = $data['nombre'] ?? '1';


/* Projets */

$nom_projets = $data['nom_projet'] ?? [];
$dates_debut = $data['date_projet'] ?? [];
$dates_fin = $data['date_fin'] ?? [];
$lieux = $data['lieu'] ?? [];
$descriptions = $data['description'] ?? [];


/* Stages */

$nom_stages = $data['nom_stage'] ?? [];
$dates_debut_stages = $data['date_debut_stage'] ?? [];
$dates_fin_stages = $data['date_fin_stage'] ?? [];
$lieux_stages = $data['lieu_stage'] ?? [];
$descriptions_stages = $data['description_stage'] ?? [];


/* Vérifier que les tableaux sont bien des tableaux */

if (!is_array($nom_projets)) $nom_projets = [];
if (!is_array($dates_debut)) $dates_debut = [];
if (!is_array($dates_fin)) $dates_fin = [];
if (!is_array($lieux)) $lieux = [];
if (!is_array($descriptions)) $descriptions = [];

if (!is_array($nom_stages)) $nom_stages = [];
if (!is_array($dates_debut_stages)) $dates_debut_stages = [];
if (!is_array($dates_fin_stages)) $dates_fin_stages = [];
if (!is_array($lieux_stages)) $lieux_stages = [];
if (!is_array($descriptions_stages)) $descriptions_stages = [];


/* 
    DÉTERMINER LE NOMBRE DE BLOCS À AFFICHER
   */

$nombre_projets = count($nom_projets);

if ($nombre_projets == 0) {

    if ($nombre == 'plus de 5') {
        $nombre_projets = 6;
    } else {
        $nombre_projets = (int)$nombre;

        if ($nombre_projets < 1) {
            $nombre_projets = 1;
        }
    }
}


/* On affiche au moins un stage */
$nombre_stages = count($nom_stages);

if ($nombre_stages < 1) {
    $nombre_stages = 1;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fiche de renseignements</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="form-container">

    <h1>Fiche de renseignements</h1>

    <p class="intro">
        Veuillez remplir les informations suivantes.
    </p>


    

    <?php

    if (isset($_GET['erreur']) && $_GET['erreur'] == 'dates') {

        echo '<p class="error">
        Vérifiez les dates : la date de fin doit être strictement
        postérieure à la date de début.
        </p>';
    }

    ?>
    <?php
if (isset($_SESSION['email_error'])) {
    echo '<p class="error">'
        . h($_SESSION['email_error']) .
        '</p>';

    unset($_SESSION['email_error']); //supprime message derreur de session
}
?>


    <form id="monFormulaire"
          action="recap.php"
          method="POST"
          enctype="multipart/form-data">


        
        <fieldset>

            <legend>Renseignements personnels</legend>


            <label for="nom">Nom :</label>

            <input
                type="text"
                id="nom"
                name="nom"
                value="<?= h($data['nom'] ?? '') ?>"
                required
            >


            <label for="prenom">Prénom :</label>

            <input
                type="text"
                id="prenom"
                name="prenom"
                value="<?= h($data['prenom'] ?? '') ?>"
                required
            >


            <label for="age">Age :</label>

            <input
                type="number"
                id="age"
                name="age"
                min="1"
                max="100"
                value="<?= h($data['age'] ?? '') ?>"
                required
            >


            <label for="telephone">
                Numéro de téléphone :
            </label>

            <input
                type="tel"
                id="telephone"
                name="numero_telephone"
                value="<?= h($data['numero_telephone'] ?? '') ?>"
                required
            >


            <label for="email">Email :</label>

<input
    type="email"
    id="email"
    name="email"
    value="<?= h($data['email'] ?? '') ?>"
    required
>

<p id="email-message"></p> 

        </fieldset>



    

        <fieldset>

            <legend>Renseignements académiques</legend>


            <label>Vous êtes en :</label>


            <div class="choice-group">

                <?php

                $filieres = [
                    '2AP',
                    'GSTR',
                    'GI',
                    'SCM',
                    'GC',
                    'MS'
                ];

                foreach ($filieres as $filiere):

                ?>

                    <label class="choice">

                        <input
                            type="radio"
                            name="filiere"
                            value="<?= h($filiere) ?>"
                            <?= (($data['filiere'] ?? '') == $filiere)
                                ? 'checked'
                                : '' ?>
                            required
                        >

                        <?= h($filiere) ?> //affichage de la filiere en evitant contenu contenant du HTML

                    </label>

                <?php endforeach; ?>

            </div>



            <label>Année :</label>


            <div class="choice-group">


                <label class="choice" id="annee1">

                    <input
                        type="radio"
                        name="annee"
                        value="1ère année"
                        <?= $annee == '1ère année' ? 'checked' : '' ?>
                        required
                    >

                    1ère année

                </label>



                <label class="choice" id="annee2">

                    <input
                        type="radio"
                        name="annee"
                        value="2ème année"
                        <?= $annee == '2ème année' ? 'checked' : '' ?>
                        required
                    >

                    2ème année

                </label>



                <label class="choice" id="annee3">

                    <input
                        type="radio"
                        name="annee"
                        value="3ème année"
                        <?= $annee == '3ème année' ? 'checked' : '' ?>
                        required
                    >

                    3ème année

                </label>

            </div>



            <h2>Modules suivis cette année</h2>


            <div
                class="checkbox-group"
                id="modules-container"
            >

                <p class="muted">
                    Veuillez choisir une filière.
                </p>

            </div>

        </fieldset>



        
        <fieldset>

            <legend>Projets réalisés</legend>


            <label for="nombre">

                Nombre de projets réalisés cette année :

            </label>


            <select id="nombre" name="nombre">

                <?php

                $nombres = [
                    '1',
                    '2',
                    '3',
                    '4',
                    '5',
                    'plus de 5'
                ];

                foreach ($nombres as $n):

                ?>

                    <option
                        value="<?= h($n) ?>"
                        <?= (string)$nombre == $n
                            ? 'selected'
                            : '' ?>
                    >

                        <?= $n == 'plus de 5'
                            ? 'Plus de 5'
                            : h($n) ?>

                    </option>

                <?php endforeach; ?>

            </select>



            <div id="projets">


                <?php

                for ($i = 0; $i < $nombre_projets; $i++):

                ?>

                    <div class="repeat-card projet">

                        <h3>
                            Projet <?= $i + 1 ?>
                        </h3>


                        <label>
                            Nom du projet :
                        </label>

                        <input
                            type="text"
                            name="nom_projet[]"
                            value="<?= h($nom_projets[$i] ?? '') ?>"
                        >


                        <label>
                            Date de début :
                        </label>

                        <input
                            type="date"
                            class="date-debut"
                            name="date_projet[]"
                            value="<?= h($dates_debut[$i] ?? '') ?>"
                        >


                        <label>
                            Date de fin :
                        </label>

                        <input
                            type="date"
                            class="date-fin"
                            name="date_fin[]"
                            value="<?= h($dates_fin[$i] ?? '') ?>"
                        >


                        <label>
                            Lieu :
                        </label>

                        <input
                            type="text"
                            name="lieu[]"
                            value="<?= h($lieux[$i] ?? '') ?>"
                        >


                        <label>
                            Description :
                        </label>

                        <textarea
                            name="description[]"
                        ><?= h($descriptions[$i] ?? '') ?></textarea>

                    </div>

                <?php endfor; ?>

            </div>



            <button
                type="button"
                class="btn-secondary"
                id="ajouterProjet"
            >
                + Ajouter un projet
            </button>

        </fieldset>



        <!-- =====================================================
             STAGES
             ===================================================== -->

        <fieldset>

            <legend>Stages réalisés</legend>


            <div id="stages">


                <?php

                for ($i = 0; $i < $nombre_stages; $i++):

                ?>

                    <div class="repeat-card stage">

                        <h3>
                            Stage <?= $i + 1 ?>
                        </h3>


                        <label>
                            Nom du stage :
                        </label>

                        <input
                            type="text"
                            name="nom_stage[]"
                            value="<?= h($nom_stages[$i] ?? '') ?>"
                        >


                        <label>
                            Date de début :
                        </label>

                        <input
                            type="date"
                            class="date-debut"
                            name="date_debut_stage[]"
                            value="<?= h($dates_debut_stages[$i] ?? '') ?>"
                        >


                        <label>
                            Date de fin :
                        </label>

                        <input
                            type="date"
                            class="date-fin"
                            name="date_fin_stage[]"
                            value="<?= h($dates_fin_stages[$i] ?? '') ?>"
                        >


                        <label>
                            Lieu :
                        </label>

                        <input
                            type="text"
                            name="lieu_stage[]"
                            value="<?= h($lieux_stages[$i] ?? '') ?>"
                        >


                        <label>
                            Description :
                        </label>

                        <textarea
                            name="description_stage[]"
                        ><?= h($descriptions_stages[$i] ?? '') ?></textarea>

                    </div>

                <?php endfor; ?>

            </div>



            <button
                type="button"
                class="btn-secondary"
                id="ajouterStage"
            >
                + Ajouter un stage
            </button>

        </fieldset>



       

        <fieldset>

            <legend>Autres informations</legend>


            <label for="centre_interet">
                Centres d'intérêt :
            </label>

            <textarea
                id="centre_interet"
                name="centre_interet"
            ><?= h($data['centre_interet'] ?? '') ?></textarea>



            <label for="competences">
                Compétences :
            </label>

            <textarea
                id="competences"
                name="competences"
            ><?= h($data['competences'] ?? '') ?></textarea>



            <label for="langues">
                Langues :
            </label>

            <input
                type="text"
                id="langues"
                name="langues"
                value="<?= h($data['langues'] ?? '') ?>"
            >



            <label for="remarques">
                Vos remarques :
            </label>

            <textarea
                id="remarques"
                name="remarques"
                rows="4"
            ><?= h($data['remarques'] ?? '') ?></textarea>



            <label for="fichier">
                Choisir un fichier :
            </label>

            <input
                type="file"
                id="fichier"
                name="fichier"
            >


            <?php if (!empty($data['fichier_nom'])): ?>

                <p class="muted">

                    Fichier déjà transmis :
                    <?= h($data['fichier_nom']) ?>

                </p>

            <?php endif; ?>

        </fieldset>



        <!-- BOUTONS -->

        <div class="buttons">

            <button
                type="submit"
                class="btn-submit"
            >
                Envoyer
            </button>


            <button
                type="button"
                class="btn-reset"
                id="effacer"
            >
                Effacer
            </button>

        </div>

    </form>

</div>



<script>



const modulesParFiliere = {

    "2AP": [
        "Mécanique",
        "Algèbre",
        "Analyse",
        "Physique",
        "Électricité"
    ],

    "GSTR": [
        "Réseaux",
        "Télécommunications",
        "Systèmes d'exploitation",
        "Architecture des réseaux",
        "Transmission"
    ],

    "GI": [
        "Compilation",
        "Web Avancée",
        "POO",
        "BD",
        "Pro Av"
    ],

    "SCM": [
        "Supply Chain",
        "Logistique",
        "Gestion des stocks",
        "Transport",
        "Management"
    ],

    "GC": [
        "Mécanique",
        "Résistance des matériaux",
        "Béton armé",
        "Construction",
        "Géotechnique"
    ],

    "MS": [
        "Management",
        "Marketing",
        "Finance",
        "Comptabilité",
        "Gestion de projet"
    ]
};


/* Modules déjà sélectionnés */

let modulesSelectionnes =
    <?= json_encode(array_values($modules)) ?>; //json_encode() transforme les modules en format JavaScript


/* Filière déjà sélectionnée */

let filierePrecedente =
    <?= json_encode($data['filiere'] ?? '') ?>;





function afficherAnnees()
{
    let filiere =
        document.querySelector(
            'input[name="filiere"]:checked'
        );

    let annee3 =
        document.getElementById("annee3");

    let radio3 =
        document.querySelector(
            'input[name="annee"][value="3ème année"]'
        );

    let radio2 =
        document.querySelector(
            'input[name="annee"][value="2ème année"]'
        );


    if (filiere && filiere.value == "2AP") {

        annee3.style.display = "none";


        if (radio3.checked) {

            radio3.checked = false;
            radio2.checked = true;

        }

    } else {

        annee3.style.display = "";

    }
}





function afficherModules()
{
    let filiere =
        document.querySelector(
            'input[name="filiere"]:checked'
        );

    let container =
        document.getElementById("modules-container");


    container.innerHTML = ""; //on vide le conteneur pour que lorsquon passe de GI a GSTR on supprime les anciens modules gi


    if (!filiere) {

        container.textContent =
            "Veuillez choisir une filière.";

        return;
    }


    let listeModules =
        modulesParFiliere[filiere.value];


    for (let i = 0; i < listeModules.length; i++) {

        let module = listeModules[i];


        let label =
            document.createElement("label");

        label.className = "choice";


        let checkbox =
            document.createElement("input");

        checkbox.type = "checkbox";

        checkbox.name = "modules[]";

        checkbox.value = module;


        if (modulesSelectionnes.includes(module)) { // conserver ce que deja selectionnee ; les module existent dans le tanbleau
            checkbox.checked = true;
        }


        checkbox.addEventListener(
            "change",
            function()
            {

                if (checkbox.checked) {

                    if (!modulesSelectionnes.includes(module)) {

                        modulesSelectionnes.push(module);

                    }

                } else {
//filter() permet de créer un nouveau tableau sans le module décoché.
                    modulesSelectionnes =
                        modulesSelectionnes.filter(
                            function(element)
                            {
                                return element != module;
                            }
                        );

                }

            }
        );


        label.appendChild(checkbox);

        label.appendChild(
            document.createTextNode(" " + module) //permet de cree label avec le type checkbox et le nom du module
        );

        container.appendChild(label); //ajoute a la page ce label
    }
}



/* Quand on change de filière */

let radiosFiliere =
    document.querySelectorAll(
        'input[name="filiere"]'
    );


radiosFiliere.forEach(
    function(radio)
    {

        radio.addEventListener(
            "change",
            function()
            {

                if (filierePrecedente != radio.value) { //Si on change réellement de filière, on efface les anciens modules sélectionnés.



                    modulesSelectionnes = [];

                    filierePrecedente = radio.value;

                }


                afficherAnnees();

                afficherModules();

            }
        );

    }
);




function configurerDates(bloc)
{
    let debut =
        bloc.querySelector(".date-debut");

    let fin =
        bloc.querySelector(".date-fin");


    function verifierDate()
    {

        if (debut.value == "") {

            fin.removeAttribute("min");

            return;
        }


        let date =
            new Date(debut.value);

        date.setDate(
            date.getDate() + 1
        );


        let annee =
            date.getFullYear();

        let mois =            //getMonth() + 1 : récupère le mois. On ajoute 1, car JavaScript commence les mois à 0 (janvier).
            String(
                date.getMonth() + 1
            ).padStart(2, "0");    //padStart(2, "0") : ajoute un zéro devant si le nombre contient un seul chiffre.

        let jour =
            String(
                date.getDate()
            ).padStart(2, "0");


        fin.min =
            annee + "-" + mois + "-" + jour;


        if (
            fin.value != "" &&
            fin.value <= debut.value
        ) {

            fin.value = "";

        }

    }


    debut.addEventListener(
        "change",
        verifierDate
    );


    fin.addEventListener(
        "change",
        function()
        {

            if (
                debut.value != "" &&
                fin.value != "" &&
                fin.value <= debut.value
            ) {

                alert(
                    "La date de fin doit être strictement postérieure à la date de début."
                );

                fin.value = "";

            }

        }
    );


    verifierDate();
}




function renumeroterProjets()
{
    let projets =
        document.querySelectorAll(
            "#projets .projet"
        );


    for (let i = 0; i < projets.length; i++) {

        projets[i].querySelector("h3").textContent =
            "Projet " + (i + 1);

    }
}


function renumeroterStages()
{
    let stages =
        document.querySelectorAll(
            "#stages .stage"
        );


    for (let i = 0; i < stages.length; i++) {

        stages[i].querySelector("h3").textContent =
            "Stage " + (i + 1);

    }
}





function actualiserNombreProjets()
{
    let projets =
        document.querySelectorAll(
            "#projets .projet"
        );


    let nombre =
        projets.length;


    let select =
        document.getElementById("nombre");


    if (nombre > 5) {

        select.value = "plus de 5";

    } else {

        select.value =
            String(nombre);

    }
}





function ajouterProjet()
{
    let div =
        document.createElement("div");


    div.className =
        "repeat-card projet";


    div.innerHTML = `

        <h3>Projet</h3>

        <label>Nom du projet :</label>

        <input
            type="text"
            name="nom_projet[]"
        >


        <label>Date de début :</label>

        <input
            type="date"
            class="date-debut"
            name="date_projet[]"
        >


        <label>Date de fin :</label>

        <input
            type="date"
            class="date-fin"
            name="date_fin[]"
        >


        <label>Lieu :</label>

        <input
            type="text"
            name="lieu[]"
        >


        <label>Description :</label>

        <textarea
            name="description[]"
        ></textarea>

    `;


    document
        .getElementById("projets")  //ajouter reelement le bloc de nv projet
        .appendChild(div);


    configurerDates(div);

    renumeroterProjets();

    actualiserNombreProjets();
}



/* Bouton ajouter projet */

document
    .getElementById("ajouterProjet")
    .addEventListener(
        "click",
        ajouterProjet
    );



/* =========================================================
   CHANGEMENT DU NOMBRE DE PROJETS
   ========================================================= */

document
    .getElementById("nombre")
    .addEventListener(
        "change",
        function()
        {

            let cible;


            if (this.value == "plus de 5") {

                cible = 6;

            } else {

                cible =
                    parseInt(
                        this.value
                    );

            }


            let projets =
                document.querySelectorAll(
                    "#projets .projet"
                );


            let total =
                projets.length;


            /* Ajouter */

            while (total < cible) {

                ajouterProjet();

                total++;

            }


            /* Supprimer */

            while (total > cible) {

                let projets =
                    document.querySelectorAll(
                        "#projets .projet"
                    );


                projets[
                    projets.length - 1
                ].remove();


                total--;

            }


            renumeroterProjets();

            actualiserNombreProjets();

        }
    );



/* =========================================================
   AJOUTER UN STAGE
   ========================================================= */

function ajouterStage()
{
    let div =
        document.createElement("div");


    div.className =
        "repeat-card stage";


    div.innerHTML = `

        <h3>Stage</h3>

        <label>Nom du stage :</label>

        <input
            type="text"
            name="nom_stage[]"
        >


        <label>Date de début :</label>

        <input
            type="date"
            class="date-debut"
            name="date_debut_stage[]"
        >


        <label>Date de fin :</label>

        <input
            type="date"
            class="date-fin"
            name="date_fin_stage[]"
        >


        <label>Lieu :</label>

        <input
            type="text"
            name="lieu_stage[]"
        >


        <label>Description :</label>

        <textarea
            name="description_stage[]"
        ></textarea>

    `;


    document
        .getElementById("stages")
        .appendChild(div); //devient visible a la page 


    configurerDates(div);

    renumeroterStages();
}



/* Bouton ajouter stage */

document
    .getElementById("ajouterStage")
    .addEventListener(
        "click",
        ajouterStage
    );



/* Configurer les dates des blocs déjà présents */

let blocs =
    document.querySelectorAll(
        ".projet, .stage"
    );


blocs.forEach(
    function(bloc)
    {
        configurerDates(bloc);
    }
);





document
    .getElementById("monFormulaire")
    .addEventListener(
        "submit",
        function(event)
        {

            let blocs =
                document.querySelectorAll(
                    ".projet, .stage"
                );


            for (
                let i = 0;
                i < blocs.length;
                i++
            ) {

                let debut =
                    blocs[i]
                        .querySelector(".date-debut")
                        .value;


                let fin =
                    blocs[i]
                        .querySelector(".date-fin")
                        .value;


                /* Une seule date remplie */

                if (
                    (debut != "" && fin == "") ||
                    (debut == "" && fin != "")
                ) {

                    alert(
                        "Veuillez remplir les deux dates ou laisser les deux vides."
                    );


                    event.preventDefault(); //empeche lenvoi du formulaire

                    return;
                }


                

                if (
                    debut != "" &&
                    fin != "" &&
                    fin <= debut
                ) {

                    alert(
                        "La date de fin doit être strictement postérieure à la date de début."
                    );


                    event.preventDefault();

                    return;
                }

            }

        }
    );





document
    .getElementById("effacer")
    .addEventListener(
        "click",
        function()
        {

            let confirmation =
                confirm(
                    "Voulez-vous vraiment effacer toutes les données ?"
                );


            if (confirmation) {

                window.location.href =
                    "formulaire.php?effacer=1";

            }

        }
    );



/* Affichage initial */

afficherAnnees();

afficherModules();

const emailInput = document.getElementById("email");
const emailMessage = document.getElementById("email-message");

emailInput.addEventListener("input", function () {

    let email = emailInput.value.trim();

    emailMessage.textContent = "";
    emailMessage.style.color = "";

    if (email === "") {
        return;
    }


    

    let syntaxeValide =
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    if (!syntaxeValide) {

        emailMessage.textContent =
            " Adresse email invalide.";

        emailMessage.style.color = "red";

        return;
    }


    

    let domaine =
        email.split("@")[1];


    /* JavaScript envoie une requête au service DNS de Google pour demander 

si le domaine possède un enregistrement MX permettant de recevoir des emails */

    fetch(
        "https://dns.google/resolve?name=" +
        encodeURIComponent(domaine) +
        "&type=MX"
    )

    .then(response => response.json())

    .then(data => {

        if (data.Answer) {

            emailMessage.textContent =
                " Email et domaine valides.";

            emailMessage.style.color = "green";

        } else {

            emailMessage.textContent =
                " Le domaine n'existe pas.";

            emailMessage.style.color = "red";

        }

    })

    .catch(() => {

        emailMessage.textContent =
            " Impossible de vérifier le domaine.";

        emailMessage.style.color = "red";

    });

});
</script>

</body>
</html>