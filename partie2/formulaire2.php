<?php

session_start();

/*
|--------------------------------------------------------------------------
| EFFACER LE CV
|--------------------------------------------------------------------------
*/

if (isset($_GET['effacer']) && $_GET['effacer'] == '1') {

    if (isset($_SESSION['cv']['photo'])) {
        $ancienPhoto = __DIR__ . '/' . $_SESSION['cv']['photo'];

        if (file_exists($ancienPhoto)) {
            unlink($ancienPhoto);
        }
    }

    unset($_SESSION['cv']);
    unset($_SESSION['cv_error']);

    header("Location: formulaire2.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| FONCTION POUR AFFICHER DU TEXTE SANS RISQUE
|--------------------------------------------------------------------------
*/

function h($texte)
{
    return htmlspecialchars($texte ?? '', ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| RÉCUPÉRER LES ANCIENNES DONNÉES
|--------------------------------------------------------------------------
*/

$cv = $_SESSION['cv'] ?? [];
$erreur = $_SESSION['cv_error'] ?? '';

unset($_SESSION['cv_error']);

$formations = $cv['formations'] ?? [];
$stages = $cv['stages'] ?? [];

/*
|--------------------------------------------------------------------------
| S'IL N'Y A AUCUNE FORMATION
|--------------------------------------------------------------------------
*/

if (empty($formations)) {
    $formations = [
        [
            'diplome' => '',
            'etablissement' => '',
            'date_debut' => '',
            'date_fin' => ''
        ]
    ];
}

/*
|--------------------------------------------------------------------------
| S'IL N'Y A AUCUN STAGE
|--------------------------------------------------------------------------
*/

if (empty($stages)) {
    $stages = [
        [
            'entreprise' => '',
            'poste' => '',
            'date_debut' => '',
            'date_fin' => '',
            'description' => ''
        ]
    ];
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Créer mon CV</title>

    <link rel="stylesheet" href="sstyle.css">
</head>

<body>

<div class="form-container">

    <h1>Créer mon CV</h1>

    <?php if ($erreur): ?>

        <div class="error-message">
            <?= h($erreur) ?>
        </div>

    <?php endif; ?>


    <form action="recap2.php" method="POST" enctype="multipart/form-data">

        <!-- ========================================================= -->
        <!-- INFORMATIONS PERSONNELLES -->
        <!-- ========================================================= -->

        <h2>Informations personnelles</h2>

        <div class="form-grid">

            <div class="form-group">
                <label>Nom :</label>
                <input
                    type="text"
                    name="nom"
                    value="<?= h($cv['nom'] ?? '') ?>"
                    required
                >
            </div>


            <div class="form-group">
                <label>Prénom :</label>
                <input
                    type="text"
                    name="prenom"
                    value="<?= h($cv['prenom'] ?? '') ?>"
                    required
                >
            </div>


            <div class="form-group">
                <label>Age :</label>
                <input
                    type="number"
                    name="age"
                    value="<?= h($cv['age'] ?? '') ?>"
                    min="1"
                    max="100"
                    required
                >
            </div>


            <div class="form-group">
                <label>Téléphone :</label>
                <input
                    type="text"
                    name="telephone"
                    value="<?= h($cv['telephone'] ?? '') ?>"
                    required
                >
            </div>


            <div class="form-group">
                <label>Email :</label>
                <input
                    type="email"
                    name="email"
                    value="<?= h($cv['email'] ?? '') ?>"
                    required
                >
            </div>


            <div class="form-group">
                <label>Adresse :</label>
                <input
                    type="text"
                    name="adresse"
                    value="<?= h($cv['adresse'] ?? '') ?>"
                    required
                >
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- PHOTO -->
        <!-- ========================================================= -->

        <h2>Photo</h2>

        <div class="form-group">

            <label>Photo :</label>

            <input
                type="file"
                name="photo"
                accept="image/*"
                <?= empty($cv['photo']) ? 'required' : '' ?>
            >

            <?php if (!empty($cv['photo'])): ?>

                <p class="photo-existante">
                    Une photo est déjà enregistrée.
                </p>

            <?php endif; ?>

        </div>


        <!-- ========================================================= -->
        <!-- FORMATIONS -->
        <!-- ========================================================= -->

        <h2>Formations</h2>

        <div id="formations-container">

            <?php foreach ($formations as $formation): ?>

                <div class="repeat-card">

                    <label>Nom de la formation :</label>

                    <input
                        type="text"
                        name="diplome[]"
                        value="<?= h($formation['diplome'] ?? '') ?>"
                    >


                    


                    <div class="date-grid">

                        <div>
                            <label>Date de début :</label>

                            <input
                                type="date"
                                name="date_debut_formation[]"
                                value="<?= h($formation['date_debut'] ?? '') ?>"
                                onchange="verifierDates(this, 'date_debut_formation[]', 'date_fin_formation[]')"
                            >
                        </div>


                        <div>
                            <label>Date de fin :</label>

                            <input
                                type="date"
                                name="date_fin_formation[]"
                                value="<?= h($formation['date_fin'] ?? '') ?>"
                                onchange="verifierDates(this, 'date_debut_formation[]', 'date_fin_formation[]')"
                            >
                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn-remove"
                        onclick="this.parentElement.remove()"
                    >
                        Supprimer
                    </button>

                </div>

            <?php endforeach; ?>

        </div>


        <button
            type="button"
            class="btn-secondary"
            onclick="ajouterFormation()"
        >
            + Ajouter une formation
        </button>


        <!-- ========================================================= -->
        <!-- STAGES -->
        <!-- ========================================================= -->

        <h2>Stages</h2>

        <div id="stages-container">

            <?php foreach ($stages as $stage): ?>

                <div class="repeat-card">

                    <label>Entreprise :</label>

                    <input
                        type="text"
                        name="entreprise[]"
                        value="<?= h($stage['entreprise'] ?? '') ?>"
                    >


                    <label>Poste :</label>

                    <input
                        type="text"
                        name="poste[]"
                        value="<?= h($stage['poste'] ?? '') ?>"
                    >


                    <div class="date-grid">

                        <div>
                            <label>Date de début :</label>

                            <input
                                type="date"
                                name="date_debut_stage[]"
                                value="<?= h($stage['date_debut'] ?? '') ?>"
                                 onchange="verifierDates(this, 'date_debut_stage[]', 'date_fin_stage[]')"
                            >
                        </div>


                        <div>
                            <label>Date de fin :</label>

                            <input
                                type="date"
                                name="date_fin_stage[]"
                                value="<?= h($stage['date_fin'] ?? '') ?>"
                                onchange="verifierDates(this, 'date_debut_stage[]', 'date_fin_stage[]')"
                            >
                        </div>

                    </div>


                    <label>Description :</label>

                    <textarea
                        name="description_stage[]"
                        rows="4"
                    ><?= h($stage['description'] ?? '') ?></textarea>


                    <button
                        type="button"
                        class="btn-remove"
                        onclick="this.parentElement.remove()"
                    >
                        Supprimer
                    </button>

                </div>

            <?php endforeach; ?>

        </div>


        <button
            type="button"
            class="btn-secondary"
            onclick="ajouterStage()"
        >
            + Ajouter un stage
        </button>


        <!-- ========================================================= -->
        <!-- COMPÉTENCES -->
        <!-- ========================================================= -->

        <h2>Compétences</h2>

        <div class="form-group">

            <textarea
                name="competences"
                rows="5"
              
            ><?= h($cv['competences'] ?? '') ?></textarea>

        </div>


        <!-- ========================================================= -->
        <!-- LANGUES -->
        <!-- ========================================================= -->

        <h2>Langues</h2>

        <div class="form-group">

            <textarea
                name="langues"
                rows="4"
                
            ><?= h($cv['langues'] ?? '') ?></textarea>

        </div>


        <!-- ========================================================= -->
        <!-- CENTRES D'INTÉRÊT -->
        <!-- ========================================================= -->

        <h2>Centres d'intérêt</h2>

        <div class="form-group">

            <textarea
                name="interets"
                rows="4"
               
            ><?= h($cv['interets'] ?? '') ?></textarea>

        </div>


        <!-- ========================================================= -->
        <!-- BOUTONS -->
        <!-- ========================================================= -->

        <div class="buttons">

            <button type="submit" class="btn-submit">
                Générer mon CV
            </button>


            <a
                href="formulaire2.php?effacer=1"
                class="btn-reset"
            >
                Effacer
            </a>

        </div>

    </form>

</div>


<script>

/*
|--------------------------------------------------------------------------
| AJOUTER UNE FORMATION
|--------------------------------------------------------------------------
*/
function verifierDates(champ, nomDebut, nomFin) {
    const carte = champ.closest(".repeat-card");

    const debut = carte.querySelector(
        'input[name="' + nomDebut + '"]'
    );

    const fin = carte.querySelector(
        'input[name="' + nomFin + '"]'
    );

    if (!debut || !fin) return;

    fin.min = debut.value;

    if (debut.value && fin.value && fin.value < debut.value) {
        fin.value = "";
        alert("La date de fin doit être égale ou postérieure à la date de début.");
    }
}
function ajouterFormation()
{
    const container = document.getElementById("formations-container");

    const div = document.createElement("div");

    div.className = "repeat-card";

    div.innerHTML = `

        <label>Nom de la formation:</label>

        <input
            type="text"
            name="diplome[]"
        >


        


        <div class="date-grid">

            <div>

                <label>Date de début :</label>

                <input
                    type="date"
                    name="date_debut_formation[]"
                    onchange="verifierDates(this, 'date_debut_formation[]', 'date_fin_formation[]')"
                >

            </div>


            <div>

                <label>Date de fin :</label>

                <input
                    type="date"
                    name="date_fin_formation[]"
                    onchange="verifierDates(this, 'date_debut_formation[]', 'date_fin_formation[]')"
                >

            </div>

        </div>


        <button
            type="button"
            class="btn-remove"
            onclick="this.parentElement.remove()"
        >
            Supprimer
        </button>

    `;

    container.appendChild(div);
}


/*
|--------------------------------------------------------------------------
| AJOUTER UN STAGE
|--------------------------------------------------------------------------
*/

function ajouterStage()
{
    const container = document.getElementById("stages-container");

    const div = document.createElement("div");

    div.className = "repeat-card";

    div.innerHTML = `

        <label>Entreprise :</label>

        <input
            type="text"
            name="entreprise[]"
        >


        <label>Poste :</label>

        <input
            type="text"
            name="poste[]"
        >


        <div class="date-grid">

            <div>

                <label>Date de début :</label>

                <input
                    type="date"
                    name="date_debut_stage[]"
                    onchange="verifierDates(this, 'date_debut_stage[]', 'date_fin_stage[]')"
                >

            </div>


            <div>

                <label>Date de fin :</label>

                <input
                    type="date"
                    name="date_fin_stage[]"
                    onchange="verifierDates(this, 'date_debut_stage[]', 'date_fin_stage[]')"
                >

            </div>

        </div>


        <label>Description :</label>

        <textarea
            name="description_stage[]"
            rows="4"
        ></textarea>


        <button
            type="button"
            class="btn-remove"
            onclick="this.parentElement.remove()"
        >
            Supprimer
        </button>

    `;

    container.appendChild(div);
}

</script>

</body>
</html>