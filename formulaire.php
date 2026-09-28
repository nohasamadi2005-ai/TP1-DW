<?php

session_start();

/*
|--------------------------------------------------------------------------
| EFFACER LE FORMULAIRE
|--------------------------------------------------------------------------
| Si on arrive avec ?effacer=1, on supprime les anciennes données.
*/

if (isset($_GET['effacer']) && $_GET['effacer'] == '1') {

    unset($_SESSION['formulaire']);

    header("Location: formulaire.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| RÉCUPÉRER LES DONNÉES DE LA SESSION
|--------------------------------------------------------------------------
*/

$data = $_SESSION['formulaire'] ?? [];


/*
|--------------------------------------------------------------------------
| MODULES
|--------------------------------------------------------------------------
*/

$modules = $data['modules'] ?? [];

if (!is_array($modules)) {
    $modules = [];
}


/*
|--------------------------------------------------------------------------
| ANNÉE
|--------------------------------------------------------------------------
*/

$annee = $data['annee'] ?? '1ère année';


/*
|--------------------------------------------------------------------------
| NOMBRE DE PROJETS
|--------------------------------------------------------------------------
*/

$nombre = $data['nombre'] ?? '1';


/*
|--------------------------------------------------------------------------
| PROJETS
|--------------------------------------------------------------------------
*/

$nom_projets = $data['nom_projet'] ?? [];
$dates_debut = $data['date_projet'] ?? [];
$dates_fin = $data['date_fin'] ?? [];
$lieux = $data['lieu'] ?? [];
$descriptions = $data['description'] ?? [];


/*
|--------------------------------------------------------------------------
| SÉCURITÉ : vérifier que les projets sont des tableaux
|--------------------------------------------------------------------------
*/

if (!is_array($nom_projets)) {
    $nom_projets = [];
}

if (!is_array($dates_debut)) {
    $dates_debut = [];
}

if (!is_array($dates_fin)) {
    $dates_fin = [];
}

if (!is_array($lieux)) {
    $lieux = [];
}

if (!is_array($descriptions)) {
    $descriptions = [];
}


/*
|--------------------------------------------------------------------------
| NOMBRE DE PROJETS À AFFICHER
|--------------------------------------------------------------------------
*/

$nombre_projets = count($nom_projets);

if ($nombre_projets == 0) {
    $nombre_projets = 1;
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

<form
    id="monFormulaire"
    action="recap.php"
    method="POST"
    enctype="multipart/form-data"
>


<!-- =========================================================
     RENSEIGNEMENTS PERSONNELS
========================================================= -->

<h2>Renseignements personnels</h2>


<label for="nom">
    Nom :
</label>

<input
    type="text"
    id="nom"
    name="nom"
    value="<?= htmlspecialchars($data['nom'] ?? '') ?>"
    required
>


<label for="prenom">
    Prénom :
</label>

<input
    type="text"
    id="prenom"
    name="prenom"
    value="<?= htmlspecialchars($data['prenom'] ?? '') ?>"
    required
>


<label for="age">
    Âge :
</label>

<input
    type="number"
    id="age"
    name="age"
    min="1"
    max="100"
    value="<?= htmlspecialchars($data['age'] ?? '') ?>"
    required
>


<label for="telephone">
    Numéro de téléphone :
</label>

<input
    type="tel"
    id="telephone"
    name="numero_telephone"
    value="<?= htmlspecialchars($data['numero_telephone'] ?? '') ?>"
    required
>


<label for="email">
    Email :
</label>

<input
    type="email"
    id="email"
    name="email"
    value="<?= htmlspecialchars($data['email'] ?? '') ?>"
    required
>


<!-- =========================================================
     RENSEIGNEMENTS ACADÉMIQUES
========================================================= -->

<h2>Renseignements académiques</h2>


<label>
    Vous êtes en :
</label>


<div class="radio-group">


<label>

    <input
        type="radio"
        name="filiere"
        value="2AP"
        <?= ($data['filiere'] ?? '') == '2AP' ? 'checked' : '' ?>
    >

    2AP

</label>


<label>

    <input
        type="radio"
        name="filiere"
        value="GSTR"
        <?= ($data['filiere'] ?? '') == 'GSTR' ? 'checked' : '' ?>
    >

    GSTR

</label>


<label>

    <input
        type="radio"
        name="filiere"
        value="GI"
        <?= ($data['filiere'] ?? '') == 'GI' ? 'checked' : '' ?>
    >

    GI

</label>


<label>

    <input
        type="radio"
        name="filiere"
        value="SCM"
        <?= ($data['filiere'] ?? '') == 'SCM' ? 'checked' : '' ?>
    >

    SCM

</label>


<label>

    <input
        type="radio"
        name="filiere"
        value="GC"
        <?= ($data['filiere'] ?? '') == 'GC' ? 'checked' : '' ?>
    >

    GC

</label>


<label>

    <input
        type="radio"
        name="filiere"
        value="MS"
        <?= ($data['filiere'] ?? '') == 'MS' ? 'checked' : '' ?>
    >

    MS

</label>


</div>


<!-- =========================================================
     ANNÉE
========================================================= -->

<label>
    Année :
</label>


<div class="radio-group">


<label id="annee1">

    <input
        type="radio"
        name="annee"
        value="1ère année"
        <?= $annee == '1ère année' ? 'checked' : '' ?>
    >

    1ère année

</label>


<label id="annee2">

    <input
        type="radio"
        name="annee"
        value="2ème année"
        <?= $annee == '2ème année' ? 'checked' : '' ?>
    >

    2ème année

</label>


<label id="annee3">

    <input
        type="radio"
        name="annee"
        value="3ème année"
        <?= $annee == '3ème année' ? 'checked' : '' ?>
    >

    3ème année

</label>


</div>


<!-- =========================================================
     MODULES
========================================================= -->


<h2>Modules suivis cette année</h2>

<div class="checkbox-group" id="modules-container">

    <!-- Les modules seront affichés ici selon la filière -->

</div>



<!-- =========================================================
     NOMBRE DE PROJETS
========================================================= -->

<h2>Nombre de projets</h2>


<label for="nombre">

    Nombre de projets réalisés cette année :

</label>


<select id="nombre" name="nombre">

    <option
        value="1"
        <?= $nombre == '1' ? 'selected' : '' ?>
    >
        1
    </option>


    <option
        value="2"
        <?= $nombre == '2' ? 'selected' : '' ?>
    >
        2
    </option>


    <option
        value="3"
        <?= $nombre == '3' ? 'selected' : '' ?>
    >
        3
    </option>


    <option
        value="4"
        <?= $nombre == '4' ? 'selected' : '' ?>
    >
        4
    </option>


    <option
        value="5"
        <?= $nombre == '5' ? 'selected' : '' ?>
    >
        5
    </option>


    <option
        value="plus de 5"
        <?= $nombre == 'plus de 5' ? 'selected' : '' ?>
    >
        Plus de 5
    </option>

</select>


<!-- =========================================================
     PROJETS
========================================================= -->

<h2>Projets réalisés</h2>


<div id="projets">


<?php for ($i = 0; $i < $nombre_projets; $i++): ?>


<div class="projet">


<h3>
    Projet <?= $i + 1 ?>
</h3>


<label>
    Nom du projet :
</label>


<input
    type="text"
    name="nom_projet[]"
    value="<?= htmlspecialchars($nom_projets[$i] ?? '') ?>"
>


<label>
    Date de début :
</label>


<input
    type="date"
    class="date-debut"
    name="date_projet[]"
    value="<?= htmlspecialchars($dates_debut[$i] ?? '') ?>"
>


<label>
    Date de fin :
</label>


<input
    type="date"
    class="date-fin"
    name="date_fin[]"
    value="<?= htmlspecialchars($dates_fin[$i] ?? '') ?>"
>


<label>
    Lieu :
</label>


<input
    type="text"
    name="lieu[]"
    value="<?= htmlspecialchars($lieux[$i] ?? '') ?>"
>


<label>
    Description :
</label>


<textarea name="description[]"><?= htmlspecialchars($descriptions[$i] ?? '') ?></textarea>


</div>


<?php endfor; ?>


</div>


<br>


<button
    type="button"
    class="btn-secondary"
    onclick="ajouterProjet()"
>
    + Ajouter un projet
</button>


<!-- =========================================================
     CENTRES D'INTÉRÊT
========================================================= -->

<h2>Centres d'intérêt</h2>


<label>
    Centre d'intérêt :
</label>


<input
    type="text"
    name="centre_interet"
    value="<?= htmlspecialchars($data['centre_interet'] ?? '') ?>"
>


<!-- =========================================================
     COMPÉTENCES
========================================================= -->

<h2>Compétences et langues</h2>


<label>
    Compétences :
</label>


<input
    type="text"
    name="competences"
    value="<?= htmlspecialchars($data['competences'] ?? '') ?>"
>


<label>
    Langues :
</label>


<input
    type="text"
    name="langues"
    value="<?= htmlspecialchars($data['langues'] ?? '') ?>"
>


<!-- =========================================================
     REMARQUES
========================================================= -->

<h2>Vos remarques</h2>


<textarea
    name="remarques"
    rows="5"
><?= htmlspecialchars($data['remarques'] ?? '') ?></textarea>


<!-- =========================================================
     FICHIER
========================================================= -->

<h2>Fichier</h2>


<label>
    Choisir un fichier :
</label>


<input
    type="file"
    name="fichier"
>


<!-- =========================================================
     BOUTONS
========================================================= -->

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
    onclick="effacerFormulaire()"
>
    Effacer
</button>


</div>


</form>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>


/*
|--------------------------------------------------------------------------
| COMPTEUR DES PROJETS
|--------------------------------------------------------------------------
*/

let numeroProjet =
    document.querySelectorAll('.projet').length;


/*
|--------------------------------------------------------------------------
| AJOUTER UN PROJET
|--------------------------------------------------------------------------
*/

function ajouterProjet() {

    numeroProjet++;


    let div = document.createElement("div");

    div.className = "projet";


    div.innerHTML = `

        <h3>
            Projet ${numeroProjet}
        </h3>


        <label>
            Nom du projet :
        </label>

        <input
            type="text"
            name="nom_projet[]"
        >


        <label>
            Date de début :
        </label>

        <input
            type="date"
            class="date-debut"
            name="date_projet[]"
        >


        <label>
            Date de fin :
        </label>

        <input
            type="date"
            class="date-fin"
            name="date_fin[]"
        >


        <label>
            Lieu :
        </label>

        <input
            type="text"
            name="lieu[]"
        >


        <label>
            Description :
        </label>

        <textarea
            name="description[]"
        ></textarea>

    `;


    document
        .getElementById("projets")
        .appendChild(div);


    configurerDates(div);
}


/*
|--------------------------------------------------------------------------
| CONTRÔLE DES DATES
|--------------------------------------------------------------------------
*/

function configurerDates(projet) {


let dateDebut = projet.querySelector(".date-debut");

let dateFin = projet.querySelector(".date-fin");


dateDebut.addEventListener("change", function() {

    if (dateDebut.value !== "") {

        dateFin.min = dateDebut.value;

    }


    if (
        dateDebut.value !== "" &&
        dateFin.value !== ""
    ) {

        let debut = new Date(dateDebut.value);

        let fin = new Date(dateFin.value);


        if (fin < debut) {

            dateFin.value = "";

            alert(
                "La date de fin ne peut pas être avant la date de début."
            );

        }

    }

});


dateFin.addEventListener("change", function() {

    if (
        dateDebut.value !== "" &&
        dateFin.value !== ""
    ) {

        let debut = new Date(dateDebut.value);

        let fin = new Date(dateFin.value);


        if (fin < debut) {

            alert(
                "Erreur : la date de fin ne peut pas être avant la date de début."
            );

            dateFin.value = "";

            dateFin.focus();

        }

    }

});


/*
|------------------------------------------------------------------
| Si une date de début existe déjà
|------------------------------------------------------------------
*/

if (dateDebut.value !== "") {

    dateFin.min = dateDebut.value;

}


}



/*
|--------------------------------------------------------------------------
| CONFIGURER LES DATES DES PROJETS EXISTANTS
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll(".projet")
    .forEach(function(projet) {

        configurerDates(projet);

    });


/*
|--------------------------------------------------------------------------
| AFFICHER LES ANNÉES SELON LA FILIÈRE
|--------------------------------------------------------------------------
*/

function afficherAnnees() {

    let filiereSelectionnee =
        document.querySelector(
            'input[name="filiere"]:checked'
        );


    if (!filiereSelectionnee) {
        return;
    }


    let filiere =
        filiereSelectionnee.value;


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


    if (filiere === "2AP") {

        /*
        | Cacher la 3ème année
        */

        annee3.style.display =
            "none";


        /*
        | Si 3ème année était sélectionnée,
        | sélectionner automatiquement 2ème année
        */

        if (radio3.checked) {

            radio3.checked = false;

            radio2.checked = true;

        }

    } else {

        /*
        | Pour les autres filières,
        | afficher la 3ème année
        */

        annee3.style.display =
            "inline-flex";

    }

}


/*
|--------------------------------------------------------------------------
| CHANGEMENT DE FILIÈRE
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll(
        'input[name="filiere"]'
    )
    .forEach(function(radio) {

        radio.addEventListener(
            "change",
            afficherAnnees
        );

    });


/*
|--------------------------------------------------------------------------
| EXÉCUTER AU CHARGEMENT
|--------------------------------------------------------------------------
*/

afficherAnnees();


/*
|--------------------------------------------------------------------------
| VALIDATION AVANT ENVOI
|--------------------------------------------------------------------------
*/

document
.getElementById("monFormulaire")
.addEventListener(
"submit",
function(event) {


        let datesDebut =
            document.querySelectorAll(
                'input[name="date_projet[]"]'
            );

        let datesFin =
            document.querySelectorAll(
                'input[name="date_fin[]"]'
            );


        for (
            let i = 0;
            i < datesDebut.length;
            i++
        ) {

            if (
                datesDebut[i].value !== "" &&
                datesFin[i].value !== ""
            ) {

                let debut =
                    new Date(datesDebut[i].value);

                let fin =
                    new Date(datesFin[i].value);


                if (fin < debut) {

                    alert(
                        "Erreur dans le projet " +
                        (i + 1) +
                        " : la date de fin doit être après ou égale à la date de début."
                    );

                    datesFin[i].focus();

                    event.preventDefault();

                    return;

                }

            }

        }

    }
);


/*
|--------------------------------------------------------------------------
| EFFACER LE FORMULAIRE
|--------------------------------------------------------------------------
*/

function effacerFormulaire() {

    /*
    | Confirmation
    */

    let confirmation =
        confirm(
            "Voulez-vous vraiment effacer toutes les données ?"
        );


    if (!confirmation) {
        return;
    }


    /*
    | Supprimer les données de session
    */

    window.location.href =
        "formulaire.php?effacer=1";

}



/*
|--------------------------------------------------------------------------
| MODULES SELON LA FILIÈRE
|--------------------------------------------------------------------------
*/

const modulesParFiliere = {

    "2AP": [
        "Algorithmique",
        "Programmation",
        "Mathématiques",
        "Architecture des ordinateurs",
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
        "Pro Av",
        "Compilation",
        "Réseaux",
        "Web Avancée",
        "POO",
        "BD"
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


/*
|--------------------------------------------------------------------------
| AFFICHER LES MODULES
|--------------------------------------------------------------------------
*/

function afficherModules() {

    let filiereSelectionnee =
        document.querySelector(
            'input[name="filiere"]:checked'
        );

    let container =
        document.getElementById("modules-container");


    if (!filiereSelectionnee) {

        container.innerHTML =
            "<p>Veuillez choisir une filière.</p>";

        return;
    }


    let filiere =
        filiereSelectionnee.value;


    let modules =
        modulesParFiliere[filiere] || [];


    container.innerHTML = "";


    modules.forEach(function(module) {

        let label =
            document.createElement("label");

        let checkbox =
            document.createElement("input");

        checkbox.type = "checkbox";

        checkbox.name = "modules[]";

        checkbox.value = module;


        /*
        | Vérifier si le module était déjà sélectionné
        */

        let modulesSelectionnes =
            <?= json_encode($modules) ?>;


        if (
            modulesSelectionnes.includes(module)
        ) {

            checkbox.checked = true;

        }


        label.appendChild(checkbox);

        label.appendChild(
            document.createTextNode(" " + module)
        );


        container.appendChild(label);

    });

}


/*
|--------------------------------------------------------------------------
| CHANGEMENT DE FILIÈRE
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('input[name="filiere"]')
    .forEach(function(radio) {

        radio.addEventListener(
            "change",
            function() {

                afficherAnnees();

                afficherModules();

            }
        );

    });


/*
|--------------------------------------------------------------------------
| AFFICHER LES MODULES AU CHARGEMENT
|--------------------------------------------------------------------------
*/

afficherModules();


</script>


</body>

</html>
