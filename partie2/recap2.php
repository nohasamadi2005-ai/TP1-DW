<?php

session_start();

/*
|--------------------------------------------------------------------------
| DOMPDF
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/connexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;


/*
|--------------------------------------------------------------------------
| FONCTION HTML
|--------------------------------------------------------------------------
*/

function h($texte)
{
    return htmlspecialchars($texte ?? '', ENT_QUOTES, 'UTF-8');
}


/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

function dateCourte($date)
{
    if (empty($date)) {
        return '';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return $date;
    }

    return date('m/Y', $timestamp);
}
function dateValide($date)
{
    $objetDate = DateTime::createFromFormat('!Y-m-d', $date);

    return $objetDate && $objetDate->format('Y-m-d') === $date;
}

/*
|--------------------------------------------------------------------------
| SI LE FORMULAIRE EST ENVOYÉ
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | RÉCUPÉRER LES INFORMATIONS
    |--------------------------------------------------------------------------
    */

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');

    $competences = trim($_POST['competences'] ?? '');
    $langues = trim($_POST['langues'] ?? '');
    $interets = trim($_POST['interets'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | FORMATIONS
    |--------------------------------------------------------------------------
    */

    $formations = [];

    $diplomes = $_POST['diplome'] ?? [];
    $datesDebutFormation = $_POST['date_debut_formation'] ?? [];
    $datesFinFormation = $_POST['date_fin_formation'] ?? [];


    $nombreFormations = max(
        count($diplomes),
        count($datesDebutFormation),
        count($datesFinFormation)
    );


    for ($i = 0; $i < $nombreFormations; $i++) {

        $diplome = trim($diplomes[$i] ?? '');
        $dateDebut = trim($datesDebutFormation[$i] ?? '');
        $dateFin = trim($datesFinFormation[$i] ?? '');


        if (
            $diplome !== '' ||
            $dateDebut !== '' ||
            $dateFin !== ''
        ) {

            $formations[] = [
                'diplome' => $diplome,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin
            ];
        }
    }

    

/*
|--------------------------------------------------------------------------
| STAGES
|--------------------------------------------------------------------------
*/

$stages = [];

$entreprises = $_POST['entreprise'] ?? [];
$postes = $_POST['poste'] ?? [];
$datesDebutStage = $_POST['date_debut_stage'] ?? [];
$datesFinStage = $_POST['date_fin_stage'] ?? [];
$descriptions = $_POST['description_stage'] ?? [];


$nombreStages = max(
    count($entreprises),
    count($postes),
    count($datesDebutStage),
    count($datesFinStage),
    count($descriptions)
);


for ($i = 0; $i < $nombreStages; $i++) {

    $entreprise = trim($entreprises[$i] ?? '');
    $poste = trim($postes[$i] ?? '');
    $dateDebut = trim($datesDebutStage[$i] ?? '');
    $dateFin = trim($datesFinStage[$i] ?? '');
    $description = trim($descriptions[$i] ?? '');

    if (
        $entreprise !== '' ||
        $poste !== '' ||
        $dateDebut !== '' ||
        $dateFin !== '' ||
        $description !== ''
    ) {

        $stages[] = [
            'entreprise' => $entreprise,
            'poste' => $poste,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'email' => $email,
            'description' => $description
        ];
    }
}

        /*
    |--------------------------------------------------------------------------
    | VALIDATION DES DATES
    |--------------------------------------------------------------------------
    */

    $erreurDate = '';

    // Vérifier les dates des formations
    foreach ($formations as $formation) {

        $debut = $formation['date_debut'];
        $fin = $formation['date_fin'];

        if (
            ($debut !== '' && !dateValide($debut)) ||
            ($fin !== '' && !dateValide($fin))
        ) {
            $erreurDate = "Une date de formation est invalide.";
            break;
        }

        if ($debut !== '' && $fin !== '' && $fin < $debut) {
            $erreurDate = "La date de fin de formation doit être égale ou postérieure à la date de début.";
            break;
        }
    }

    // Vérifier les dates des stages
    if ($erreurDate === '') {

        foreach ($stages as $stage) {

            $debut = $stage['date_debut'];
            $fin = $stage['date_fin'];

            if (
                ($debut !== '' && !dateValide($debut)) ||
                ($fin !== '' && !dateValide($fin))
            ) {
                $erreurDate = "Une date de stage est invalide.";
                break;
            }

            if ($debut !== '' && $fin !== '' && $fin < $debut) {
                $erreurDate = "La date de fin du stage doit être égale ou postérieure à la date de début.";
                break;
            }
        }
    }

   
    if ($erreurDate !== '') {

        $_SESSION['cv'] = [
            'nom' => $nom,
            'prenom' => $prenom,
            'age' => $age,
            'telephone' => $telephone,
            'email' => $email,
            'adresse' => $adresse,
            'photo' => $_SESSION['cv']['photo'] ?? '',
            'formations' => $formations,
            'stages' => $stages,
            'competences' => $competences,
            'langues' => $langues,
            'interets' => $interets
        ];

        $_SESSION['cv_error'] = $erreurDate;

        header("Location: formulaire2.php");
        exit;
    }


    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // On conserve les informations saisies avant de revenir au formulaire.
        $_SESSION['cv'] = [
            'nom' => $nom,
            'prenom' => $prenom,
            'age' => $age,
            'telephone' => $telephone,
            'email' => $email,
            'adresse' => $adresse,
            'photo' => $_SESSION['cv']['photo'] ?? '',
            'formations' => $formations,
            'stages' => $stages,
            'competences' => $competences,
            'langues' => $langues,
            'interets' => $interets
        ];

        $_SESSION['cv_error'] = "Adresse email invalide : vérifiez sa syntaxe.";

        header("Location: formulaire2.php");
        exit;
    }

    $domaine = substr(strrchr($email, '@'), 1);

    $domaineExiste =
        checkdnsrr($domaine, 'MX') ||
        checkdnsrr($domaine, 'A') ||
        checkdnsrr($domaine, 'AAAA');

    if (!$domaineExiste) {

        // Même en cas d'erreur, toutes les informations restent dans la session.
        $_SESSION['cv'] = [
            'nom' => $nom,
            'prenom' => $prenom,
            'age' => $age,
            'telephone' => $telephone,
            'email' => $email,
            'adresse' => $adresse,
            'photo' => $_SESSION['cv']['photo'] ?? '',
            'formations' => $formations,
            'stages' => $stages,
            'competences' => $competences,
            'langues' => $langues,
            'interets' => $interets
        ];

        $_SESSION['cv_error'] =
            "Le domaine de l'adresse email n'existe pas ou ne possède pas de configuration DNS valide.";

        header("Location: formulaire2.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | PHOTO
    |--------------------------------------------------------------------------
    */

    $ancienCV = $_SESSION['cv'] ?? [];

    $photoPath = $ancienCV['photo'] ?? '';


    if (
        isset($_FILES['photo']) &&
        $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {

            $_SESSION['cv_error'] = "Erreur lors de l'envoi de la photo.";

            header("Location: formulaire2.php");
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | VÉRIFIER QUE C'EST BIEN UNE IMAGE
        |--------------------------------------------------------------------------
        */

        $imageInfo = getimagesize($_FILES['photo']['tmp_name']);

        if ($imageInfo === false) {

            $_SESSION['cv_error'] = "Le fichier sélectionné n'est pas une image.";

            header("Location: formulaire2.php");
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | EXTENSION
        |--------------------------------------------------------------------------
        */

        $extension = strtolower(
            pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)
        );


        $extensionsAutorisees = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ];


        if (!in_array($extension, $extensionsAutorisees)) {

            $_SESSION['cv_error'] =
                "Format de photo non autorisé. Utilisez JPG, JPEG, PNG, GIF ou WEBP.";

            header("Location: formulaire2.php");
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | CRÉER LE DOSSIER UPLOADS
        |--------------------------------------------------------------------------
        */

        $uploadsDir = __DIR__ . '/uploads';


        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }


        /*
        |--------------------------------------------------------------------------
        | SUPPRIMER L'ANCIENNE PHOTO
        |--------------------------------------------------------------------------
        */

        if (!empty($photoPath)) {

            $ancienChemin = __DIR__ . '/' . $photoPath;

            if (file_exists($ancienChemin)) {
                unlink($ancienChemin);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | NOM UNIQUE
        |--------------------------------------------------------------------------
        */

        $nouveauNom =
            uniqid('photo_', true) . '.' . $extension;


        $destination =
            $uploadsDir . '/' . $nouveauNom;


        /*
        |--------------------------------------------------------------------------
        | DÉPLACER LA PHOTO
        |--------------------------------------------------------------------------
        */

        if (!move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            $destination
        )) {

            $_SESSION['cv_error'] =
                "Impossible d'enregistrer la photo.";

            header("Location: formulaire2.php");
            exit;
        }


        $photoPath = 'uploads/' . $nouveauNom;
    }


    /*
    |--------------------------------------------------------------------------
    | PHOTO OBLIGATOIRE
    |--------------------------------------------------------------------------
    */

    if (empty($photoPath)) {

        $_SESSION['cv_error'] =
            "La photo est obligatoire.";

        header("Location: formulaire2.php");
        exit;
    }

    /*
|--------------------------------------------------------------------------
| ENREGISTRER L'UTILISATEUR
|--------------------------------------------------------------------------
*/

$sql = "
    INSERT INTO utilisateur
    (
        email,
        nom,
        prenom,
        tel,
        adresse,
        photo,
        age
    )
    VALUES
    (
        :email,
        :nom,
        :prenom,
        :tel,
        :adresse,
        :photo,
        :age
    )
    ON DUPLICATE KEY UPDATE
        nom = :nom2,
        prenom = :prenom2,
        age = :age2,
        tel = :tel2,
        adresse = :adresse2,
        photo = :photo2
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':email' => $email,
    ':nom' => $nom,
    ':prenom' => $prenom,
    ':age' => $age,
    ':tel' => $telephone,
    ':adresse' => $adresse,
    ':photo' => $photoPath,

    ':nom2' => $nom,
    ':prenom2' => $prenom,
    ':age2' => $age,
    ':tel2' => $telephone,
    ':adresse2' => $adresse,
    ':photo2' => $photoPath
]);


/* 
|-------------------------------------------------------------------------- 
| ENREGISTRER LES FORMATIONS
|-------------------------------------------------------------------------- 
*/

$stmt = $pdo->prepare(
    "DELETE FROM formation WHERE email = ?"
);

$stmt->execute([$email]);

$stmtFormation = $pdo->prepare("
    INSERT INTO formation
    (
        NomF,
        date_debut,
        date_fin,
        email
    )
    VALUES
    (
        :nom,
        :date_debut,
        :date_fin,
        :email
    )
");

foreach ($formations as $formation) {

    $stmtFormation->execute([
        ':nom' => $formation['diplome'],
        ':date_debut' => $formation['date_debut'] ?: null,
        ':date_fin' => $formation['date_fin'] ?: null,
        ':email' => $email
    ]);
}


/* 
|-------------------------------------------------------------------------- 
| ENREGISTRER LES STAGES
|-------------------------------------------------------------------------- 
*/

$stmt = $pdo->prepare(
    "DELETE FROM stage WHERE email = ?"
);

$stmt->execute([$email]);

$stmtStage = $pdo->prepare("
    INSERT INTO stage
    (
        entreprise,
        poste,
        date_debut,
        date_fin,
        email,
        description
    )
    VALUES
    (
        :entreprise,
        :poste,
        :date_debut,
        :date_fin,
        :email,
        :description
    )
");

foreach ($stages as $stage) {

    $stmtStage->execute([
        ':entreprise' => $stage['entreprise'],
        ':poste' => $stage['poste'] ?: null,
        ':date_debut' => $stage['date_debut'] ?: null,
        ':date_fin' => $stage['date_fin'] ?: null,
        ':email' => $email,
        ':description' => $stage['description'] ?: null
    ]);
}


/*
|--------------------------------------------------------------------------
| ENREGISTRER LES LANGUES
|--------------------------------------------------------------------------
*/

$listeLangues = preg_split(
    '/[\r\n,;]+/',
    $langues,
    -1,
    PREG_SPLIT_NO_EMPTY
);

$listeLangues = array_unique(
    array_map('trim', $listeLangues)
);

/* Supprimer les anciennes relations de cet utilisateur */
$stmt = $pdo->prepare("DELETE FROM parler WHERE email = ?");
$stmt->execute([$email]);

/* Préparer les requêtes */
$stmtRechercheLangue = $pdo->prepare("
    SELECT IdL
    FROM langue
    WHERE libelle = ?
    LIMIT 1
");

$stmtAjoutLangue = $pdo->prepare("
    INSERT INTO langue (libelle)
    VALUES (?)
");

$stmtAjoutParler = $pdo->prepare("
    INSERT INTO parler (email, IdL)
    VALUES (?, ?)
");

foreach ($listeLangues as $langue) {

    if ($langue === '') {
        continue;
    }

    /* Chercher si la langue existe déjà */
    $stmtRechercheLangue->execute([$langue]);

    $idLangue = $stmtRechercheLangue->fetchColumn();

    /* Si elle n'existe pas, la créer */
    if (!$idLangue) {

        $stmtAjoutLangue->execute([$langue]);

        $idLangue = $pdo->lastInsertId();
    }

    /* Associer la langue à l'utilisateur */
    $stmtAjoutParler->execute([
        $email,
        $idLangue
    ]);
}


/*
|--------------------------------------------------------------------------
| ENREGISTRER LES COMPÉTENCES
|--------------------------------------------------------------------------
*/

$listeCompetences = preg_split(
    '/[\r\n,;]+/',
    $competences,
    -1,
    PREG_SPLIT_NO_EMPTY
);

$listeCompetences = array_unique(
    array_map('trim', $listeCompetences)
);

/* Supprimer les anciennes relations */
$stmt = $pdo->prepare("DELETE FROM maitriser WHERE email = ?");
$stmt->execute([$email]);

/* Préparer les requêtes */
$stmtRechercheCompetence = $pdo->prepare("
    SELECT IdComp
    FROM competence
    WHERE libelle = ?
    LIMIT 1
");

$stmtAjoutCompetence = $pdo->prepare("
    INSERT INTO competence (libelle)
    VALUES (?)
");

$stmtAjoutMaitriser = $pdo->prepare("
    INSERT INTO maitriser (email, IdComp)
    VALUES (?, ?)
");

foreach ($listeCompetences as $competence) {

    if ($competence === '') {
        continue;
    }

    /* Chercher si la compétence existe déjà */
    $stmtRechercheCompetence->execute([$competence]);

    $idCompetence = $stmtRechercheCompetence->fetchColumn();

    /* Si elle n'existe pas, la créer */
    if (!$idCompetence) {

        $stmtAjoutCompetence->execute([$competence]);

        $idCompetence = $pdo->lastInsertId();
    }

    /* Associer la compétence à l'utilisateur */
    $stmtAjoutMaitriser->execute([
        $email,
        $idCompetence
    ]);
}


/*
|--------------------------------------------------------------------------
| ENREGISTRER LES CENTRES D'INTÉRÊT
|--------------------------------------------------------------------------
*/

$listeInterets = preg_split(
    '/[\r\n,;]+/',
    $interets,
    -1,
    PREG_SPLIT_NO_EMPTY
);

$listeInterets = array_unique(
    array_map('trim', $listeInterets)
);

/* Supprimer les anciennes relations */
$stmt = $pdo->prepare("DELETE FROM avoir WHERE email = ?");
$stmt->execute([$email]);

/* Préparer les requêtes */
$stmtRechercheInteret = $pdo->prepare("
    SELECT IdC
    FROM centre_interet
    WHERE libelle = ?
    LIMIT 1
");

$stmtAjoutInteret = $pdo->prepare("
    INSERT INTO centre_interet (libelle)
    VALUES (?)
");

$stmtAjoutAvoir = $pdo->prepare("
    INSERT INTO avoir (email, IdC)
    VALUES (?, ?)
");

foreach ($listeInterets as $interet) {

    if ($interet === '') {
        continue;
    }

    /* Chercher si le centre d'intérêt existe déjà */
    $stmtRechercheInteret->execute([$interet]);

    $idInteret = $stmtRechercheInteret->fetchColumn();

    /* Si elle n'existe pas, le créer */
    if (!$idInteret) {

        $stmtAjoutInteret->execute([$interet]);

        $idInteret = $pdo->lastInsertId();
    }

    /* Associer le centre d'intérêt à l'utilisateur */
    $stmtAjoutAvoir->execute([
        $email,
        $idInteret
    ]);
}
    /*
    |--------------------------------------------------------------------------
    | SAUVEGARDER LE CV EN SESSION
    |--------------------------------------------------------------------------
    */

    $_SESSION['cv'] = [

        'nom' => $nom,
        'prenom' => $prenom,
        'age' => $age,
        'telephone' => $telephone,
        'email' => $email,
        'adresse' => $adresse,

        'photo' => $photoPath,

        'formations' => $formations,

        'stages' => $stages,

        'competences' => $competences,

        'langues' => $langues,

        'interets' => $interets
    ];
}


/*
|--------------------------------------------------------------------------
| RÉCUPÉRER LE CV
|--------------------------------------------------------------------------
*/

$cv = $_SESSION['cv'] ?? [];


if (empty($cv)) {

    header("Location: formulaire2.php");
    exit;
}




if (isset($_GET['pdf']) && $_GET['pdf'] == '1') {


    

    $options = new Options();

    $options->set('isRemoteEnabled', true);

    $options->set('defaultFont', 'DejaVu Sans');


    $dompdf = new Dompdf($options);


    /*
    |--------------------------------------------------------------------------
    | PHOTO EN BASE64
    |--------------------------------------------------------------------------
    */

    $photoBase64 = '';

    if (!empty($cv['photo'])) {

        $photoFile = __DIR__ . '/' . $cv['photo'];

        if (file_exists($photoFile)) {

            $extension = strtolower(
                pathinfo($photoFile, PATHINFO_EXTENSION)
            );

            $mime = 'image/jpeg';

            if ($extension === 'png') {
                $mime = 'image/png';
            } elseif ($extension === 'gif') {
                $mime = 'image/gif';
            } elseif ($extension === 'webp') {
                $mime = 'image/webp';
            }

            $photoBase64 =
                'data:' . $mime . ';base64,' .
                base64_encode(file_get_contents($photoFile));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATIONS HTML
    |--------------------------------------------------------------------------
    */

    $formationsHTML = '';

    foreach ($cv['formations'] ?? [] as $formation) {

        $formationsHTML .= '

            <div class="item">

                <div class="item-title">
                    ' . h($formation['diplome']) . '
                </div>

                <div class="item-date">
                    ' .
                    h(dateCourte($formation['date_debut'])) .
                    ' - ' .
                    h(dateCourte($formation['date_fin'])) .
                    '
                </div>

            </div>

        ';
    }


    /*
    |--------------------------------------------------------------------------
    | STAGES HTML
    |--------------------------------------------------------------------------
    */

    $stagesHTML = '';

    foreach ($cv['stages'] ?? [] as $stage) {

        $stagesHTML .= '

            <div class="item">

                <div class="item-title">
                    ' . h($stage['poste']) . '
                </div>

                <div class="item-place">
                    ' . h($stage['entreprise']) . '
                </div>

                <div class="item-date">
                    ' .
                    h(dateCourte($stage['date_debut'])) .
                    ' - ' .
                    h(dateCourte($stage['date_fin'])) .
                    '
                </div>

                <div class="description">
                    ' . nl2br(h($stage['description'])) . '
                </div>

            </div>

        ';
    }


    /*
    |--------------------------------------------------------------------------
    | HTML DU PDF
    |--------------------------------------------------------------------------
    */

    $html = '

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<style>

@page {
    margin: 0;
}

body {

    margin: 0;

    font-family: DejaVu Sans, sans-serif;

    font-size: 11px;

    color: #333;
}


.page {

    width: 210mm;

    min-height: 297mm;

}


table {

    width: 100%;

    border-collapse: collapse;
}


.sidebar {

    width: 72mm;

    background: #e8f3ef;

    vertical-align: top;

    padding: 12mm 8mm;

}


.main {

    width: 138mm;

    vertical-align: top;

    padding: 12mm 10mm;

}


.photo {

    width: 25mm;

    height: 25mm;

    object-fit: cover;

    margin-bottom: 8mm;

}


.cv-title {

    text-align: center;

    font-size: 25px;

    font-weight: bold;

    margin-bottom: 5px;

}


.name {

    text-align: center;

    font-size: 17px;

    font-weight: bold;

    margin-bottom: 15px;

}


.section-title {

    font-size: 14px;

    font-weight: bold;

    color: #2d6a5a;

    border-bottom: 1px solid #2d6a5a;

    padding-bottom: 3px;

    margin-top: 14px;

    margin-bottom: 8px;

}


.sidebar .section-title {

    color: #2d6a5a;

}


.contact {

    line-height: 1.6;

}


.item {

    margin-bottom: 12px;

}


.item-title {

    font-size: 12px;

    font-weight: bold;

}


.item-place {

    font-size: 11px;

    margin-top: 2px;

}


.item-date {

    font-size: 10px;

    color: #777;

    margin-top: 2px;

}


.description {

    margin-top: 5px;

    line-height: 1.5;

}


.text {

    line-height: 1.5;

}

</style>

</head>


<body>

<div class="page">


<table>

<tr>


<!-- ========================================================= -->
<!-- SIDEBAR -->
<!-- ========================================================= -->

<td class="sidebar">


';

    if ($photoBase64 !== '') {

        $html .= '

            <img
                src="' . $photoBase64 . '"
                class="photo"
            >

        ';
    }


    $html .= '

    <div class="section-title">
        CONTACT
    </div>


    <div class="contact">

        <strong>Email :</strong><br>
        ' . h($cv['email']) . '<br><br>

        <strong>Téléphone :</strong><br>
        ' . h($cv['telephone']) . '<br><br>

        <strong>Adresse :</strong><br>
        ' . h($cv['adresse']) . '<br><br>

        <strong>Âge :</strong>
        ' . h($cv['age']) . ' ans

    </div>


    <div class="section-title">
        COMPÉTENCES
    </div>


    <div class="text">
        ' . nl2br(h($cv['competences'])) . '
    </div>


    <div class="section-title">
        LANGUES
    </div>


    <div class="text">
        ' . nl2br(h($cv['langues'])) . '
    </div>


    <div class="section-title">
        CENTRES D\'INTÉRÊT
    </div>


    <div class="text">
        ' . nl2br(h($cv['interets'])) . '
    </div>


</td>


<!-- ========================================================= -->
<!-- MAIN -->
<!-- ========================================================= -->

<td class="main">


    <div class="cv-title">
        Curriculum Vitae
    </div>


    <div class="name">

        ' .
        h($cv['prenom']) .
        ' ' .
        h($cv['nom']) .
        '

    </div>


    <div class="section-title">
        FORMATIONS
    </div>


    ' . $formationsHTML . '


    <div class="section-title">
        EXPÉRIENCES / STAGES
    </div>


    ' . $stagesHTML . '


</td>


</tr>

</table>

</div>

</body>

</html>

';


    /*
    |--------------------------------------------------------------------------
    | GÉNÉRER LE PDF
    |--------------------------------------------------------------------------
    */

    $dompdf->loadHtml($html, 'UTF-8');

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();


    /*
    |--------------------------------------------------------------------------
    | TÉLÉCHARGEMENT
    |--------------------------------------------------------------------------
    */

    $dompdf->stream(
        'Mon_CV.pdf',
        [
            'Attachment' => true
        ]
    );

    exit;
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

    <title>Mon CV</title>

    <link rel="stylesheet" href="style.css">

</head>


<body>


<div class="cv-page">


    <!-- ========================================================= -->
    <!-- CV COMPLET -->
    <!-- ========================================================= -->

    <div class="cv-sheet">


        <!-- ===================================================== -->
        <!-- TITRE DU CV -->
        <!-- ===================================================== -->

        <header class="cv-header">

            <h1>
                Curriculum Vitae
            </h1>


            <div class="cv-person-name">

                <?= h($cv['prenom']) ?>

                <?= h($cv['nom']) ?>

            </div>

        </header>


        <!-- ===================================================== -->
        <!-- CONTENU DU CV : SIDEBAR + PARTIE PRINCIPALE -->
        <!-- ===================================================== -->

        <div class="cv-content">


            <!-- ================================================= -->
            <!-- SIDEBAR -->
            <!-- ================================================= -->

            <aside class="cv-sidebar">


                <?php if (!empty($cv['photo'])): ?>

                    <img
                        src="<?= h($cv['photo']) ?>"
                        class="cv-photo"
                        alt="Photo"
                        width="90"
                        height="90"
                    >

                <?php endif; ?>


                <!-- ================================================= -->
                <!-- CONTACT -->
                <!-- ================================================= -->

                <div class="cv-side-section">

                    <h3>
                        CONTACT
                    </h3>


                    <p>

                        <strong>Email :</strong><br>

                        <?= h($cv['email']) ?>

                    </p>


                    <p>

                        <strong>Téléphone :</strong><br>

                        <?= h($cv['telephone']) ?>

                    </p>


                    <p>

                        <strong>Adresse :</strong><br>

                        <?= h($cv['adresse']) ?>

                    </p>


                    <p>

                        <strong>Âge :</strong>

                        <?= h($cv['age']) ?> ans

                    </p>

                </div>


                <!-- ================================================= -->
                <!-- COMPÉTENCES -->
                <!-- ================================================= -->

                <div class="cv-side-section">

                    <h3>
                        COMPÉTENCES
                    </h3>


                    <p>

                        <?= nl2br(h($cv['competences'])) ?>

                    </p>

                </div>


                <!-- ================================================= -->
                <!-- LANGUES -->
                <!-- ================================================= -->

                <div class="cv-side-section">

                    <h3>
                        LANGUES
                    </h3>


                    <p>

                        <?= nl2br(h($cv['langues'])) ?>

                    </p>

                </div>


                <!-- ================================================= -->
                <!-- CENTRES D'INTÉRÊT -->
                <!-- ================================================= -->

                <div class="cv-side-section">

                    <h3>
                        CENTRES D'INTÉRÊT
                    </h3>


                    <p>

                        <?= nl2br(h($cv['interets'])) ?>

                    </p>

                </div>


            </aside>


            <!-- ================================================= -->
            <!-- PARTIE PRINCIPALE -->
            <!-- ================================================= -->

            <main class="cv-main">


                <!-- ================================================= -->
                <!-- FORMATIONS -->
                <!-- ================================================= -->

                <section class="cv-section">

                    <h2>
                        Formations
                    </h2>


                    <?php foreach ($cv['formations'] as $formation): ?>

                        <div class="cv-item">


                            <h3>

                                <?= h($formation['diplome']) ?>

                            </h3>



                            <p class="cv-date">

                                <?= h(dateCourte($formation['date_debut'])) ?>

                                -

                                <?= h(dateCourte($formation['date_fin'])) ?>

                            </p>


                        </div>

                    <?php endforeach; ?>


                </section>


                <!-- ================================================= -->
                <!-- STAGES -->
                <!-- ================================================= -->

                <section class="cv-section">

                    <h2>
                        Expériences / Stages
                    </h2>


                    <?php foreach ($cv['stages'] as $stage): ?>

                        <div class="cv-item">


                            <h3>

                                <?= h($stage['poste']) ?>

                            </h3>


                            <p class="cv-place">

                                <?= h($stage['entreprise']) ?>

                            </p>


                            <p class="cv-date">

                                <?= h(dateCourte($stage['date_debut'])) ?>

                                -

                                <?= h(dateCourte($stage['date_fin'])) ?>

                            </p>


                            <p>

                                <?= nl2br(h($stage['description'])) ?>

                            </p>


                        </div>

                    <?php endforeach; ?>


                </section>


            </main>


        </div>


    </div>


    <!-- ========================================================= -->
    <!-- BOUTONS SOUS LE CV -->
    <!-- ========================================================= -->

    <div class="cv-buttons">


        <a
            href="formulaire2.php"
            class="btn-secondary"
        >
            Modifier mon CV
        </a>


        <a
            href="recap2.php?pdf=1"
            class="btn-submit"
        >
            Télécharger mon CV
        </a>


    </div>


</div>


</body>

</html>

