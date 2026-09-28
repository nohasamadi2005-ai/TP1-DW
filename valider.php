
<?php

session_start();


/*
|------------------------------------------------------------------
| VÉRIFIER QUE LES DONNÉES EXISTENT
|------------------------------------------------------------------
*/

if (!isset($_SESSION['formulaire'])) {

    header("Location: formulaire.php");

    exit;
}


/*
|------------------------------------------------------------------
| RÉCUPÉRER LES DONNÉES
|------------------------------------------------------------------
*/

$data = $_SESSION['formulaire'];


/*
|------------------------------------------------------------------
| INFORMATIONS PERSONNELLES
|------------------------------------------------------------------
*/

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


/*
|------------------------------------------------------------------
| INFORMATIONS ACADÉMIQUES
|------------------------------------------------------------------
*/

$filiere =
    $data['filiere'] ?? '';

$annee =
    $data['annee'] ?? '';

$modules =
    $data['modules'] ?? [];

$nombre =
    $data['nombre'] ?? '';


/*
|------------------------------------------------------------------
| PROJETS
|------------------------------------------------------------------
*/

$nom_projets =
    $data['nom_projet'] ?? [];

$dates_debut =
    $data['date_projet'] ?? [];

$dates_fin =
    $data['date_fin'] ?? [];

$lieux =
    $data['lieu'] ?? [];

$descriptions =
    $data['description'] ?? [];


/*
|------------------------------------------------------------------
| AUTRES INFORMATIONS
|------------------------------------------------------------------
*/

$centre_interet =
    $data['centre_interet'] ?? '';

$competences =
    $data['competences'] ?? '';

$langues =
    $data['langues'] ?? '';

$remarques =
    $data['remarques'] ?? '';


/*
|------------------------------------------------------------------
| VÉRIFICATION DES DATES
|------------------------------------------------------------------
*/

for ($i = 0; $i < count($nom_projets); $i++) {

    $dateDebut =
        $dates_debut[$i] ?? '';

    $dateFin =
        $dates_fin[$i] ?? '';


    if ($dateDebut !== '' && $dateFin !== '') {

        $debut =
            DateTime::createFromFormat(
                'Y-m-d',
                $dateDebut
            );

        $fin =
            DateTime::createFromFormat(
                'Y-m-d',
                $dateFin
            );


        if (!$debut || !$fin) {

            die(
                "Erreur : date invalide dans le projet " .
                ($i + 1)
            );

        }


        /*
        | Comparaison année + mois + jour
        */

        if ($fin < $debut) {

            die(
                "Erreur dans le projet " .
                ($i + 1) .
                " : la date de fin doit être après ou égale à la date de début."
            );

        }

    }

}


/*
|------------------------------------------------------------------
| PRÉPARER LE CONTENU DU FICHIER
|------------------------------------------------------------------
*/

$contenu = "";

$contenu .= "========================================\n";
$contenu .= "FICHE DE RENSEIGNEMENTS\n";
$contenu .= "========================================\n\n";


/*
|------------------------------------------------------------------
| RENSEIGNEMENTS PERSONNELS
|------------------------------------------------------------------
*/

$contenu .= "RENSEIGNEMENTS PERSONNELS\n";
$contenu .= "-------------------------\n";

$contenu .= "Nom : " . $nom . "\n";

$contenu .= "Prénom : " . $prenom . "\n";

$contenu .= "Âge : " . $age . "\n";

$contenu .= "Téléphone : " . $telephone . "\n";

$contenu .= "Email : " . $email . "\n\n";


/*
|------------------------------------------------------------------
| RENSEIGNEMENTS ACADÉMIQUES
|------------------------------------------------------------------
*/

$contenu .= "RENSEIGNEMENTS ACADÉMIQUES\n";
$contenu .= "--------------------------\n";

$contenu .= "Filière : " . $filiere . "\n";

$contenu .= "Année : " . $annee . "\n";

$contenu .= "Nombre de projets : " . $nombre . "\n";


$contenu .= "Modules : ";

if (!empty($modules)) {

    $contenu .= implode(", ", $modules);

} else {

    $contenu .= "Aucun";

}

$contenu .= "\n\n";


/*
|------------------------------------------------------------------
| PROJETS
|------------------------------------------------------------------
*/

$contenu .= "PROJETS RÉALISÉS\n";
$contenu .= "----------------\n";


for ($i = 0; $i < count($nom_projets); $i++) {

    $contenu .= "\n";

    $contenu .= "Projet " . ($i + 1) . "\n";

    $contenu .= "Nom : " .
        ($nom_projets[$i] ?? '') .
        "\n";

    $contenu .= "Date de début : " .
        ($dates_debut[$i] ?? '') .
        "\n";

    $contenu .= "Date de fin : " .
        ($dates_fin[$i] ?? '') .
        "\n";

    $contenu .= "Lieu : " .
        ($lieux[$i] ?? '') .
        "\n";

    $contenu .= "Description : " .
        ($descriptions[$i] ?? '') .
        "\n";

}


/*
|------------------------------------------------------------------
| CENTRE D'INTÉRÊT
|------------------------------------------------------------------
*/

$contenu .= "\n";

$contenu .= "CENTRES D'INTÉRÊT\n";
$contenu .= "-----------------\n";

$contenu .= $centre_interet . "\n\n";


/*
|------------------------------------------------------------------
| COMPÉTENCES
|------------------------------------------------------------------
*/

$contenu .= "COMPÉTENCES\n";
$contenu .= "-----------\n";

$contenu .= $competences . "\n\n";


/*
|------------------------------------------------------------------
| LANGUES
|------------------------------------------------------------------
*/

$contenu .= "LANGUES\n";
$contenu .= "-------\n";

$contenu .= $langues . "\n\n";


/*
|------------------------------------------------------------------
| REMARQUES
|------------------------------------------------------------------
*/

$contenu .= "REMARQUES\n";
$contenu .= "---------\n";

$contenu .= $remarques . "\n\n";


/*
|------------------------------------------------------------------
| DATE D'ENREGISTREMENT
|------------------------------------------------------------------
*/

$contenu .= "Date d'enregistrement : " .
    date("d/m/Y H:i:s") .
    "\n";

$contenu .= "========================================\n";


/*
|------------------------------------------------------------------
| CRÉER LE DOSSIER "formulaires"
|------------------------------------------------------------------
*/

$dossier = "formulaires";


if (!is_dir($dossier)) {

    mkdir($dossier, 0777, true);

}


/*
|------------------------------------------------------------------
| CHERCHER LE PROCHAIN NUMÉRO
|------------------------------------------------------------------
*/

$numero = 1;


while (
    file_exists(
        $dossier . "/formulaire_" . $numero . ".txt"
    )
) {

    $numero++;

}


/*
|------------------------------------------------------------------
| NOM DU NOUVEAU FICHIER
|------------------------------------------------------------------
*/

$fichier =
    $dossier . "/formulaire_" . $numero . ".txt";


/*
|------------------------------------------------------------------
| ENREGISTRER LE FORMULAIRE
|------------------------------------------------------------------
*/

$resultat = file_put_contents(
    $fichier,
    $contenu,
    LOCK_EX
);


/*
|------------------------------------------------------------------
| VÉRIFIER L'ENREGISTREMENT
|------------------------------------------------------------------
*/

if ($resultat === false) {

    die(
        "Erreur : impossible d'enregistrer les informations."
    );

}

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


<style>

body {

    font-family: Arial, sans-serif;

    background:
        linear-gradient(
            135deg,
            #eef2f7,
            #dce6f2
        );

    margin: 0;

    padding: 30px;

}


.container {

    max-width: 700px;

    margin: 80px auto;

    background: white;

    padding: 40px;

    border-radius: 15px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,0.1);

    text-align: center;

}


h1 {

    color: #28a745;

}


p {

    font-size: 17px;

}


.btn {

    display: inline-block;

    margin-top: 25px;

    padding: 12px 25px;

    background: #0066cc;

    color: white;

    text-decoration: none;

    border-radius: 6px;

}

</style>

</head>

<body>

<div class="container">


<h1>
    Formulaire validé avec succès !
</h1>


<p>

    Les informations ont été enregistrées dans :

    <strong>
        formulaires/formulaire_<?php echo $numero; ?>.txt
    </strong>

</p>


<a
    href="formulaire.php"
    class="btn"
>
    Retour au formulaire
</a>


</div>

</body>

</html>

