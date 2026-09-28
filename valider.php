<?php

session_start();


/* =========================================================
   1. VÉRIFIER QUE LES DONNÉES EXISTENT
   ========================================================= */

if (
    !isset($_SESSION['formulaire']) ||
    !is_array($_SESSION['formulaire'])
) {

    header('Location: formulaire.php');

    exit;
}


$data =
    $_SESSION['formulaire'];



/* =========================================================
   2. VÉRIFIER LES DATES
   ========================================================= */

function verifierDates(
    $datesDebut,
    $datesFin,
    $type
) {

    /* Vérifier que ce sont bien des tableaux */

    if (
        !is_array($datesDebut) ||
        !is_array($datesFin)
    ) {

        die("Erreur : données de dates invalides.");

    }


    /*
       On prend le plus grand nombre
       entre les dates de début et de fin.
    */

    $total =
        max(
            count($datesDebut),
            count($datesFin)
        );


    for (
        $i = 0;
        $i < $total;
        $i++
    ) {


        $debut =
            $datesDebut[$i] ?? '';

        $fin =
            $datesFin[$i] ?? '';


        /*
           Les deux dates sont vides :
           c'est autorisé.
        */

        if (
            $debut == '' &&
            $fin == ''
        ) {

            continue;

        }


        /*
           Une seule date est remplie :
           ce n'est pas autorisé.
        */

        if (
            $debut == '' ||
            $fin == ''
        ) {

            die(
                "Erreur : les deux dates doivent être remplies dans le "
                . $type
                . " "
                . ($i + 1)
                . "."
            );

        }


        /*
           Transformer les chaînes en dates
        */

        $dateDebut =
            DateTime::createFromFormat(
                '!Y-m-d',
                $debut
            );


        $dateFin =
            DateTime::createFromFormat(
                '!Y-m-d',
                $fin
            );


        /*
           Vérifier que les dates sont valides
           et que la fin est après le début.
        */

        if (
            !$dateDebut ||
            !$dateFin ||
            $dateDebut->format('Y-m-d') != $debut ||
            $dateFin->format('Y-m-d') != $fin ||
            $dateFin <= $dateDebut
        ) {

            die(
                "Erreur dans le "
                . $type
                . " "
                . ($i + 1)
                . " : la date de fin doit être strictement postérieure à la date de début."
            );

        }

    }

}



/* Vérifier les dates des projets */

verifierDates(
    $data['date_projet'] ?? [],
    $data['date_fin'] ?? [],
    'projet'
);


/* Vérifier les dates des stages */

verifierDates(
    $data['date_debut_stage'] ?? [],
    $data['date_fin_stage'] ?? [],
    'stage'
);



/* =========================================================
   3. RÉCUPÉRER LES DONNÉES SIMPLES
   ========================================================= */

$nom =
    $data['nom'] ?? '';

$prenom =
    $data['prenom'] ?? '';

$age =
    $data['age'] ?? '';

$telephone =
    $data['numero_telephone'] ?? '';

$email =
    $data['email'] ?? '';

$filiere =
    $data['filiere'] ?? '';

$annee =
    $data['annee'] ?? '';

$nombre =
    $data['nombre'] ?? '';

$centreInteret =
    $data['centre_interet'] ?? '';

$competences =
    $data['competences'] ?? '';

$langues =
    $data['langues'] ?? '';

$remarques =
    $data['remarques'] ?? '';

$fichierNom =
    $data['fichier_nom'] ?? '';


/* =========================================================
   4. RÉCUPÉRER LES MODULES
   ========================================================= */

$modules =
    $data['modules'] ?? [];

if (!is_array($modules)) {
    $modules = [];
}



/* =========================================================
   5. COMMENCER LE CONTENU DU FICHIER TXT
   ========================================================= */

$contenu =
    "========================================\n";

$contenu .=
    "FICHE DE RENSEIGNEMENTS\n";

$contenu .=
    "========================================\n\n";



/* =========================================================
   6. INFORMATIONS PERSONNELLES
   ========================================================= */

$contenu .=
    "RENSEIGNEMENTS PERSONNELS\n";

$contenu .=
    "-------------------------\n";

$contenu .=
    "Nom : " . $nom . "\n";

$contenu .=
    "Prénom : " . $prenom . "\n";

$contenu .=
    "Âge : " . $age . "\n";

$contenu .=
    "Téléphone : " . $telephone . "\n";

$contenu .=
    "Email : " . $email . "\n\n";



/* =========================================================
   7. INFORMATIONS ACADÉMIQUES
   ========================================================= */

$contenu .=
    "RENSEIGNEMENTS ACADÉMIQUES\n";

$contenu .=
    "--------------------------\n";

$contenu .=
    "Filière : " . $filiere . "\n";

$contenu .=
    "Année : " . $annee . "\n";

$contenu .=
    "Nombre de projets : " . $nombre . "\n";


if (count($modules) > 0) {

    $contenu .=
        "Modules : "
        . implode(', ', $modules)
        . "\n";

} else {

    $contenu .=
        "Modules : Aucun\n";
}


$contenu .= "\n";



/* =========================================================
   8. PROJETS
   ========================================================= */

$nomProjets =
    $data['nom_projet'] ?? [];

$debutProjets =
    $data['date_projet'] ?? [];

$finProjets =
    $data['date_fin'] ?? [];

$lieuxProjets =
    $data['lieu'] ?? [];

$descriptionsProjets =
    $data['description'] ?? [];


if (!is_array($nomProjets)) {
    $nomProjets = [];
}

if (!is_array($debutProjets)) {
    $debutProjets = [];
}

if (!is_array($finProjets)) {
    $finProjets = [];
}

if (!is_array($lieuxProjets)) {
    $lieuxProjets = [];
}

if (!is_array($descriptionsProjets)) {
    $descriptionsProjets = [];
}


$contenu .=
    "PROJETS RÉALISÉS\n";

$contenu .=
    "----------------\n";


$numeroProjet = 0;


for (
    $i = 0;
    $i < count($nomProjets);
    $i++
) {


    $nomProjet =
        $nomProjets[$i] ?? '';

    $debutProjet =
        $debutProjets[$i] ?? '';

    $finProjet =
        $finProjets[$i] ?? '';

    $lieuProjet =
        $lieuxProjets[$i] ?? '';

    $descriptionProjet =
        $descriptionsProjets[$i] ?? '';


    /*
       Si tout est vide,
       on ne sauvegarde pas ce projet.
    */

    if (
        trim(
            $nomProjet .
            $debutProjet .
            $finProjet .
            $lieuProjet .
            $descriptionProjet
        ) == ''
    ) {

        continue;

    }


    $numeroProjet++;


    $contenu .=
        "\nProjet "
        . $numeroProjet
        . "\n";

    $contenu .=
        "Nom : "
        . $nomProjet
        . "\n";

    $contenu .=
        "Date de début : "
        . $debutProjet
        . "\n";

    $contenu .=
        "Date de fin : "
        . $finProjet
        . "\n";

    $contenu .=
        "Lieu : "
        . $lieuProjet
        . "\n";

    $contenu .=
        "Description : "
        . $descriptionProjet
        . "\n";
}


if ($numeroProjet == 0) {

    $contenu .=
        "Aucun projet renseigné.\n";
}



/* =========================================================
   9. STAGES
   ========================================================= */

$nomStages =
    $data['nom_stage'] ?? [];

$debutStages =
    $data['date_debut_stage'] ?? [];

$finStages =
    $data['date_fin_stage'] ?? [];

$lieuxStages =
    $data['lieu_stage'] ?? [];

$descriptionsStages =
    $data['description_stage'] ?? [];


if (!is_array($nomStages)) {
    $nomStages = [];
}

if (!is_array($debutStages)) {
    $debutStages = [];
}

if (!is_array($finStages)) {
    $finStages = [];
}

if (!is_array($lieuxStages)) {
    $lieuxStages = [];
}

if (!is_array($descriptionsStages)) {
    $descriptionsStages = [];
}


$contenu .=
    "\nSTAGES RÉALISÉS\n";

$contenu .=
    "---------------\n";


$numeroStage = 0;


for (
    $i = 0;
    $i < count($nomStages);
    $i++
) {


    $nomStage =
        $nomStages[$i] ?? '';

    $debutStage =
        $debutStages[$i] ?? '';

    $finStage =
        $finStages[$i] ?? '';

    $lieuStage =
        $lieuxStages[$i] ?? '';

    $descriptionStage =
        $descriptionsStages[$i] ?? '';


    if (
        trim(
            $nomStage .
            $debutStage .
            $finStage .
            $lieuStage .
            $descriptionStage
        ) == ''
    ) {

        continue;

    }


    $numeroStage++;


    $contenu .=
        "\nStage "
        . $numeroStage
        . "\n";

    $contenu .=
        "Nom : "
        . $nomStage
        . "\n";

    $contenu .=
        "Date de début : "
        . $debutStage
        . "\n";

    $contenu .=
        "Date de fin : "
        . $finStage
        . "\n";

    $contenu .=
        "Lieu : "
        . $lieuStage
        . "\n";

    $contenu .=
        "Description : "
        . $descriptionStage
        . "\n";
}


if ($numeroStage == 0) {

    $contenu .=
        "Aucun stage renseigné.\n";
}



/* =========================================================
   10. AUTRES INFORMATIONS
   ========================================================= */

$contenu .=
    "\nAUTRES INFORMATIONS\n";

$contenu .=
    "-------------------\n";

$contenu .=
    "Centres d'intérêt : "
    . $centreInteret
    . "\n";

$contenu .=
    "Compétences : "
    . $competences
    . "\n";

$contenu .=
    "Langues : "
    . $langues
    . "\n";

$contenu .=
    "Remarques : "
    . $remarques
    . "\n";

$contenu .=
    "Fichier transmis : "
    . $fichierNom
    . "\n\n";


/* Date d'enregistrement */

$contenu .=
    "Date d'enregistrement : "
    . date('d/m/Y H:i:s')
    . "\n";


$contenu .=
    "========================================\n";



/* =========================================================
   11. CRÉER LE DOSSIER
   ========================================================= */

$dossier =
    __DIR__ . '/formulaires';


if (!is_dir($dossier)) {

    if (!mkdir($dossier, 0755, true)) {

        die(
            "Erreur : impossible de créer le dossier formulaires."
        );

    }

}



/* =========================================================
   12. CHOISIR UN NOM DE FICHIER
   ========================================================= */

$numeroFichier = 1;


while (true) {

    $nomFichier =
        "formulaire_"
        . $numeroFichier
        . ".txt";


    $chemin =
        $dossier
        . "/"
        . $nomFichier;


    if (!file_exists($chemin)) {

        break;

    }


    $numeroFichier++;

}



/* =========================================================
   13. ENREGISTRER LE FICHIER
   ========================================================= */

$resultat =
    file_put_contents(
        $chemin,
        $contenu
    );


if ($resultat === false) {

    die(
        "Erreur : impossible d'enregistrer les informations."
    );

}


/* Chemin affiché à l'utilisateur */

$cheminAffiche =
    "formulaires/"
    . $nomFichier;

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Validation</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>

<div class="success-container">


    <h1>
        Formulaire validé avec succès !
    </h1>


    <p>
        Les informations ont été enregistrées dans :
    </p>


    <p>

        <strong>
            <?= htmlspecialchars(
                $cheminAffiche,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>

    </p>


    <a
        href="formulaire.php"
        class="button-link btn-modifier"
    >
        Retour au formulaire
    </a>


</div>

</body>

</html>