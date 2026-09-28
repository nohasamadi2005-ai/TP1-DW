<?php

session_start();


/*
|--------------------------------------------------------------------------
| VÉRIFIER QUE LE FORMULAIRE A ÉTÉ ENVOYÉ
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: formulaire.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| ENREGISTRER LES DONNÉES DANS LA SESSION
|--------------------------------------------------------------------------
*/

$_SESSION['formulaire'] = $_POST;


/*
|--------------------------------------------------------------------------
| INFORMATIONS PERSONNELLES
|--------------------------------------------------------------------------
*/

$nom =
    $_POST['nom'] ?? '';

$prenom =
    $_POST['prenom'] ?? '';

$age =
    $_POST['age'] ?? '';

$telephone =
    $_POST['numero_telephone'] ?? '';

$email =
    $_POST['email'] ?? '';


/*
|--------------------------------------------------------------------------
| INFORMATIONS ACADÉMIQUES
|--------------------------------------------------------------------------
*/

$filiere =
    $_POST['filiere'] ?? '';

$annee =
    $_POST['annee'] ?? '';

$nombre =
    $_POST['nombre'] ?? '';


/*
|--------------------------------------------------------------------------
| MODULES
|--------------------------------------------------------------------------
*/

$modules =
    $_POST['modules'] ?? [];


if (!is_array($modules)) {

    $modules = [];

}


/*
|--------------------------------------------------------------------------
| PROJETS
|--------------------------------------------------------------------------
*/

$nom_projets =
    $_POST['nom_projet'] ?? [];

$dates_debut =
    $_POST['date_projet'] ?? [];

$dates_fin =
    $_POST['date_fin'] ?? [];

$lieux =
    $_POST['lieu'] ?? [];

$descriptions =
    $_POST['description'] ?? [];


/*
|--------------------------------------------------------------------------
| SÉCURITÉ : vérifier les tableaux
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
| AUTRES INFORMATIONS
|--------------------------------------------------------------------------
*/

$centre_interet =
    $_POST['centre_interet'] ?? '';

$competences =
    $_POST['competences'] ?? '';

$langues =
    $_POST['langues'] ?? '';

$remarques =
    $_POST['remarques'] ?? '';


/*
|--------------------------------------------------------------------------
| FICHIER
|--------------------------------------------------------------------------
*/

$nom_fichier = '';


if (
    isset($_FILES['fichier']) &&
    $_FILES['fichier']['error'] === UPLOAD_ERR_OK
) {

    $nom_fichier =
        $_FILES['fichier']['name'];

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

    <title>Récapitulatif</title>


    <style>


        * {
            box-sizing: border-box;
        }


        body {

            font-family:
                Arial,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #eef2f7,
                    #dce6f2
                );

            margin: 0;

            padding: 30px;

            color: #333;

        }


        .container {

            max-width: 900px;

            margin: auto;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.1);

        }


        h1 {

            text-align: center;

            color: #1d3557;

            margin-bottom: 35px;

        }


        h2 {

            color: #0066cc;

            border-bottom:
                2px solid #0066cc;

            padding-bottom: 8px;

            margin-top: 30px;

        }


        h3 {

            color: #333;

        }


        .information {

            background: #f7f9fc;

            padding: 12px;

            margin:
                8px 0;

            border-radius: 6px;

        }


        .information strong {

            color: #222;

        }


        ul {

            background: #f7f9fc;

            padding: 20px 40px;

            border-radius: 8px;

        }


        .projet {

            border:
                1px solid #d5dce5;

            padding: 20px;

            margin-bottom: 20px;

            border-radius: 10px;

            background:
                #fafcff;

        }


        .projet h3 {

            margin-top: 0;

            color: #0066cc;

        }


        .projet p {

            line-height: 1.6;

        }


        .buttons {

            text-align: center;

            margin-top: 35px;

        }


        button {

            border: none;

            padding:
                12px 25px;

            margin: 5px;

            border-radius: 6px;

            cursor: pointer;

            font-size: 16px;

        }


        .modifier {

            background:
                #f39c12;

            color: white;

        }


        .modifier:hover {

            background:
                #d68910;

        }


        .valider {

            background:
                #28a745;

            color: white;

        }


        .valider:hover {

            background:
                #218838;

        }


    </style>

</head>


<body>


<div class="container">


<h1>
    Fiche de renseignements
</h1>


<!-- =========================================================
     INFORMATIONS PERSONNELLES
========================================================= -->

<h2>
    Renseignements personnels
</h2>


<div class="information">

    <strong>
        Nom :
    </strong>

    <?= htmlspecialchars($nom) ?>

</div>


<div class="information">

    <strong>
        Prénom :
    </strong>

    <?= htmlspecialchars($prenom) ?>

</div>


<div class="information">

    <strong>
        Âge :
    </strong>

    <?= htmlspecialchars($age) ?>

</div>


<div class="information">

    <strong>
        Numéro de téléphone :
    </strong>

    <?= htmlspecialchars($telephone) ?>

</div>


<div class="information">

    <strong>
        Email :
    </strong>

    <?= htmlspecialchars($email) ?>

</div>


<!-- =========================================================
     INFORMATIONS ACADÉMIQUES
========================================================= -->

<h2>
    Renseignements académiques
</h2>


<div class="information">

    <strong>
        Filière :
    </strong>

    <?= htmlspecialchars($filiere) ?>

</div>


<div class="information">

    <strong>
        Année :
    </strong>

    <?= htmlspecialchars($annee) ?>

</div>


<div class="information">

    <strong>
        Nombre de projets :
    </strong>

    <?= htmlspecialchars($nombre) ?>

</div>


<h3>
    Modules suivis cette année
</h3>


<?php if (!empty($modules)): ?>


<ul>

<?php foreach ($modules as $module): ?>

    <li>

        <?= htmlspecialchars($module) ?>

    </li>

<?php endforeach; ?>

</ul>


<?php else: ?>


<p>
    Aucun module sélectionné.
</p>


<?php endif; ?>


<!-- =========================================================
     PROJETS
========================================================= -->

<h2>
    Projets réalisés
</h2>


<?php

$nombre_projets =
    count($nom_projets);

?>


<?php if ($nombre_projets > 0): ?>


<?php for (
    $i = 0;
    $i < $nombre_projets;
    $i++
): ?>


<div class="projet">


<h3>
    Projet <?= $i + 1 ?>
</h3>


<p>

    <strong>
        Nom du projet :
    </strong>

    <?= htmlspecialchars(
        $nom_projets[$i] ?? ''
    ) ?>

</p>


<p>

    <strong>
        Date de début :
    </strong>

    <?= htmlspecialchars(
        $dates_debut[$i] ?? ''
    ) ?>

</p>


<p>

    <strong>
        Date de fin :
    </strong>

    <?= htmlspecialchars(
        $dates_fin[$i] ?? ''
    ) ?>

</p>


<p>

    <strong>
        Lieu :
    </strong>

    <?= htmlspecialchars(
        $lieux[$i] ?? ''
    ) ?>

</p>


<p>

    <strong>
        Description :
    </strong>

    <br>


    <?= nl2br(
        htmlspecialchars(
            $descriptions[$i] ?? ''
        )
    ) ?>

</p>


</div>


<?php endfor; ?>


<?php else: ?>


<p>
    Aucun projet renseigné.
</p>


<?php endif; ?>


<!-- =========================================================
     CENTRE D'INTÉRÊT
========================================================= -->

<h2>
    Centres d'intérêt
</h2>


<p>

<?= nl2br(
    htmlspecialchars(
        $centre_interet
    )
) ?>

</p>


<!-- =========================================================
     COMPÉTENCES
========================================================= -->

<h2>
    Compétences
</h2>


<p>

<?= nl2br(
    htmlspecialchars(
        $competences
    )
) ?>

</p>


<!-- =========================================================
     LANGUES
========================================================= -->

<h2>
    Langues
</h2>


<p>

<?= nl2br(
    htmlspecialchars(
        $langues
    )
) ?>

</p>


<!-- =========================================================
     REMARQUES
========================================================= -->

<h2>
    Vos remarques
</h2>


<p>

<?= nl2br(
    htmlspecialchars(
        $remarques
    )
) ?>

</p>


<!-- =========================================================
     FICHIER
========================================================= -->

<h2>
    Fichier
</h2>


<?php if ($nom_fichier !== ''): ?>


<p>

    Fichier reçu :

    <strong>
        <?= htmlspecialchars($nom_fichier) ?>
    </strong>

</p>


<?php else: ?>


<p>
    Aucun fichier envoyé.
</p>


<?php endif; ?>


<!-- =========================================================
     BOUTONS
========================================================= -->

<div class="buttons">


<!-- MODIFIER -->

<form
    action="formulaire.php"
    method="GET"
    style="display:inline;"
>


<button
    type="submit"
    class="modifier"
>

    Modifier

</button>


</form>


<!-- VALIDER -->

<form
    action="valider.php"
    method="POST"
    style="display:inline;"
>


<button
    type="submit"
    class="valider"
>

    Valider

</button>


</form>


</div>


</div>


</body>

</html>
