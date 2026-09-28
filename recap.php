<?php

session_start();


/* =========================================================
   1. RÉCUPÉRER LES DONNÉES
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /* Données envoyées par formulaire.php */

    $data = $_POST;


    /* Récupérer les anciennes données */

    $ancienneDonnee =
        $_SESSION['formulaire'] ?? [];


    /* =====================================================
       2. GESTION DU FICHIER
       ===================================================== */

    /*
       Si aucun nouveau fichier n'est choisi,
       on garde l'ancien fichier.
    */

    $data['fichier_nom'] =
        $ancienneDonnee['fichier_nom'] ?? '';

    $data['fichier_stocke'] =
        $ancienneDonnee['fichier_stocke'] ?? '';


    /*
       Vérifier si un fichier a été envoyé
    */

    if (
        isset($_FILES['fichier']) &&
        $_FILES['fichier']['error'] != UPLOAD_ERR_NO_FILE
    ) {

        /* Vérifier s'il y a une erreur */

        if (
            $_FILES['fichier']['error'] != UPLOAD_ERR_OK
        ) {

            die("Erreur lors du téléchargement du fichier.");

        }


        /* Dossier où le fichier sera enregistré */

        $dossierUpload =
            __DIR__ . '/uploads';


        /*
           Créer le dossier s'il n'existe pas
        */

        if (!is_dir($dossierUpload)) {

            if (!mkdir($dossierUpload, 0755, true)) {

                die(
                    "Impossible de créer le dossier uploads."
                );

            }

        }


        /* Nom original */

        $nomOriginal =
            basename(
                $_FILES['fichier']['name']
            );


        /*
           Remplacer les caractères problématiques
           par _
        */

        $nomSecurise =
            preg_replace(
                '/[^A-Za-z0-9._-]/',
                '_',
                $nomOriginal
            );


        /*
           Donner un nom unique au fichier
           pour éviter les conflits
        */

        $nomStocke =
            uniqid() . '_' . $nomSecurise;


        $destination =
            $dossierUpload . '/' . $nomStocke;


        /* Déplacer le fichier */

        if (
            !move_uploaded_file(
                $_FILES['fichier']['tmp_name'],
                $destination
            )
        ) {

            die(
                "Impossible d'enregistrer le fichier."
            );

        }


        /*
           Supprimer l'ancien fichier
           s'il existait
        */

        $ancienFichier =
            $ancienneDonnee['fichier_stocke'] ?? '';


        if (
            $ancienFichier != '' &&
            is_file(__DIR__ . '/' . $ancienFichier)
        ) {

            unlink(
                __DIR__ . '/' . $ancienFichier
            );

        }


        /*
           Enregistrer les informations du nouveau fichier
        */

        $data['fichier_nom'] =
            $nomOriginal;

        $data['fichier_stocke'] =
            'uploads/' . $nomStocke;

    }


    /*
       Sauvegarder toutes les données
       dans la session
    */

    $_SESSION['formulaire'] =
        $data;


} elseif (isset($_SESSION['formulaire'])) {

    /*
       Si on arrive sur recap.php sans POST,
       mais que la session existe,
       on utilise les données de la session.
    */

    $data =
        $_SESSION['formulaire'];


} else {

    /*
       Aucune donnée :
       retour au formulaire
    */

    header('Location: formulaire.php');

    exit;
}



/* =========================================================
   3. FONCTION POUR AFFICHER LES DONNÉES
   ========================================================= */

function h($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}



/* =========================================================
   4. RÉCUPÉRER LES TABLEAUX
   ========================================================= */

$modules =
    $data['modules'] ?? [];

if (!is_array($modules)) {
    $modules = [];
}


/* Projets */

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


/* Stages */

$nom_stages =
    $data['nom_stage'] ?? [];

$dates_debut_stages =
    $data['date_debut_stage'] ?? [];

$dates_fin_stages =
    $data['date_fin_stage'] ?? [];

$lieux_stages =
    $data['lieu_stage'] ?? [];

$descriptions_stages =
    $data['description_stage'] ?? [];


/*
   Si quelque chose n'est pas un tableau,
   on le transforme en tableau vide.
*/

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

    <link rel="stylesheet" href="style.css">

</head>


<body>

<div class="recap-container">


    <h1>Récapitulatif de la fiche</h1>


    <!-- =====================================================
         INFORMATIONS PERSONNELLES
         ===================================================== -->

    <section>

        <h2>Renseignements personnels</h2>


        <div class="information">

            <strong>Nom :</strong>

            <?= h($data['nom'] ?? '') ?>

        </div>


        <div class="information">

            <strong>Prénom :</strong>

            <?= h($data['prenom'] ?? '') ?>

        </div>


        <div class="information">

            <strong>Âge :</strong>

            <?= h($data['age'] ?? '') ?>

        </div>


        <div class="information">

            <strong>Numéro de téléphone :</strong>

            <?= h($data['numero_telephone'] ?? '') ?>

        </div>


        <div class="information">

            <strong>Email :</strong>

            <?= h($data['email'] ?? '') ?>

        </div>

    </section>



    <!-- =====================================================
         INFORMATIONS ACADÉMIQUES
         ===================================================== -->

    <section>

        <h2>Renseignements académiques</h2>


        <div class="information">

            <strong>Filière :</strong>

            <?= h($data['filiere'] ?? '') ?>

        </div>


        <div class="information">

            <strong>Année :</strong>

            <?= h($data['annee'] ?? '') ?>

        </div>


        <div class="information">

            <strong>Nombre de projets :</strong>

            <?= h($data['nombre'] ?? '') ?>

        </div>


        <h3>Modules suivis cette année</h3>


        <?php if (count($modules) > 0): ?>

            <ul>

                <?php foreach ($modules as $module): ?>

                    <li>
                        <?= h($module) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        <?php else: ?>

            <p class="muted">
                Aucun module sélectionné.
            </p>

        <?php endif; ?>

    </section>



    <!-- =====================================================
         PROJETS
         ===================================================== -->

    <section>

        <h2>Projets réalisés</h2>


        <?php

        $numeroProjet = 0;


        for (
            $i = 0;
            $i < count($nom_projets);
            $i++
        ) {


            $nom =
                $nom_projets[$i] ?? '';

            $debut =
                $dates_debut[$i] ?? '';

            $fin =
                $dates_fin[$i] ?? '';

            $lieu =
                $lieux[$i] ?? '';

            $description =
                $descriptions[$i] ?? '';


            /*
               Vérifier si le projet contient
               au moins une information.
            */

            if (
                trim(
                    $nom .
                    $debut .
                    $fin .
                    $lieu .
                    $description
                ) == ''
            ) {

                continue;

            }


            $numeroProjet++;

        ?>


            <div class="repeat-card">


                <h3>
                    Projet <?= $numeroProjet ?>
                </h3>


                <p>

                    <strong>Nom :</strong>

                    <?= h($nom) ?>

                </p>


                <p>

                    <strong>Date de début :</strong>

                    <?= h($debut) ?>

                </p>


                <p>

                    <strong>Date de fin :</strong>

                    <?= h($fin) ?>

                </p>


                <p>

                    <strong>Lieu :</strong>

                    <?= h($lieu) ?>

                </p>


                <p>

                    <strong>Description :</strong>
                    <br>

                    <?= nl2br(h($description)) ?>

                </p>


            </div>


        <?php

        }


        if ($numeroProjet == 0):

        ?>

            <p class="muted">
                Aucun projet renseigné.
            </p>

        <?php endif; ?>

    </section>



    <!-- =====================================================
         STAGES
         ===================================================== -->

    <section>

        <h2>Stages réalisés</h2>


        <?php

        $numeroStage = 0;


        for (
            $i = 0;
            $i < count($nom_stages);
            $i++
        ) {


            $nom =
                $nom_stages[$i] ?? '';

            $debut =
                $dates_debut_stages[$i] ?? '';

            $fin =
                $dates_fin_stages[$i] ?? '';

            $lieu =
                $lieux_stages[$i] ?? '';

            $description =
                $descriptions_stages[$i] ?? '';


            /*
               Vérifier si le stage contient
               au moins une information.
            */

            if (
                trim(
                    $nom .
                    $debut .
                    $fin .
                    $lieu .
                    $description
                ) == ''
            ) {

                continue;

            }


            $numeroStage++;

        ?>


            <div class="repeat-card">


                <h3>
                    Stage <?= $numeroStage ?>
                </h3>


                <p>

                    <strong>Nom :</strong>

                    <?= h($nom) ?>

                </p>


                <p>

                    <strong>Date de début :</strong>

                    <?= h($debut) ?>

                </p>


                <p>

                    <strong>Date de fin :</strong>

                    <?= h($fin) ?>

                </p>


                <p>

                    <strong>Lieu :</strong>

                    <?= h($lieu) ?>

                </p>


                <p>

                    <strong>Description :</strong>
                    <br>

                    <?= nl2br(h($description)) ?>

                </p>


            </div>


        <?php

        }


        if ($numeroStage == 0):

        ?>

            <p class="muted">
                Aucun stage renseigné.
            </p>

        <?php endif; ?>

    </section>



    <!-- =====================================================
         AUTRES INFORMATIONS
         ===================================================== -->

    <section>

        <h2>Autres informations</h2>


        <div class="information">

            <strong>Centres d'intérêt :</strong>
            <br>

            <?= nl2br(
                h($data['centre_interet'] ?? '')
            ) ?>

        </div>


        <div class="information">

            <strong>Compétences :</strong>
            <br>

            <?= nl2br(
                h($data['competences'] ?? '')
            ) ?>

        </div>


        <div class="information">

            <strong>Langues :</strong>
            <br>

            <?= nl2br(
                h($data['langues'] ?? '')
            ) ?>

        </div>


        <div class="information">

            <strong>Remarques :</strong>
            <br>

            <?= nl2br(
                h($data['remarques'] ?? '')
            ) ?>

        </div>


        <div class="information">

            <strong>Fichier :</strong>

            <?= h(
                $data['fichier_nom']
                ?? 'Aucun fichier envoyé'
            ) ?>

        </div>

    </section>



    <!-- =====================================================
         BOUTONS
         ===================================================== -->

    <div class="buttons">


        <a
            class="button-link btn-modifier"
            href="formulaire.php"
        >
            Modifier
        </a>


        <form
            action="valider.php"
            method="POST"
            class="inline-form"
        >

            <button
                type="submit"
                class="btn-submit"
            >
                Valider
            </button>

        </form>


    </div>


</div>

</body>
</html>