<?php
require 'vendor/autoload.php'; // Include the Composer autoloader

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "odoo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['exporter'])) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Reference');
        $sheet->setCellValue('C1', 'Collection');
        $sheet->setCellValue('D1', 'Prix d\'achat');
        $sheet->setCellValue('E1', 'Prix Atout');
        $sheet->setCellValue('F1', 'Prix Stella');
        $sheet->setCellValue('G1', 'Categorie');
        $sheet->setCellValue('H1', 'Code Barre');
        $sheet->setCellValue('I1', 'Matiere');

        $sql = "SELECT * FROM produits";
        $result = $conn->query($sql);

        $rowCounter = 2;

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sheet->setCellValue('A' . $rowCounter, $row['nom']);
                $sheet->setCellValue('B' . $rowCounter, $row['reference']);
                $sheet->setCellValue('C' . $rowCounter, $row['collect']);
                $sheet->setCellValue('D' . $rowCounter, $row['prixAchat']);
                $sheet->setCellValue('E' . $rowCounter, $row['prixAtout']);
                $sheet->setCellValue('F' . $rowCounter, $row['prixStella']);
                $sheet->setCellValue('G' . $rowCounter, $row['categorie']);
                $sheet->setCellValue('H' . $rowCounter, $row['codeBarre']);
                $sheet->setCellValue('I' . $rowCounter, $row['matiere']);

                $rowCounter++;
            }
        }

        $filename = 'export_produits.csv';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->save($filename);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        readfile($filename);

        unlink($filename);

        exit();
    }
}


$sql1 = "SELECT nom FROM categories";
$result1 = $conn->query($sql1);

$categories = array();

while ($row = $result1->fetch_assoc()) {
    $categories[] = $row['nom'];
}

$sql2 = "SELECT nom FROM collections";
$result2 = $conn->query($sql2);

$collections = array();

while ($row = $result2->fetch_assoc()) {
    $collections[] = $row['nom'];
}

$sql3 = "SELECT nom FROM matieres";
$result3 = $conn->query($sql3);

$matieres = array();

while ($row = $result3->fetch_assoc()) {
    $matieres[] = $row['nom'];
}

$sql = "SELECT codeBarre FROM produits ORDER BY codeBarre DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $dernierNombre = $row['codeBarre'];
    $nouveauNombre = $dernierNombre + 1;
    $nouveauNombreComplet = strval($nouveauNombre);
} else {
    $nouveauNombreComplet = 1;
}
$nomProduit = isset($_POST['nom']) ? $_POST['nom'] : '';
$reference = isset($_POST['reference']) ? $_POST['reference'] : '';
$collection = isset($_POST['collect']) ? $_POST['collect'] : '';
$prixAchat = isset($_POST['prixAchat']) ? $_POST['prixAchat'] : '';
$prixAtout = isset($_POST['prixAtout']) ? $_POST['prixAtout'] : '';
$prixStella = isset($_POST['prixStella']) ? $_POST['prixStella'] : '';
$categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';
$matiere = isset($_POST['matiere']) ? $_POST['matiere'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomProduit = isset($_POST['nom']) ? $_POST['nom'] : '';
    $reference = isset($_POST['reference']) ? $_POST['reference'] : '';
    $collection = isset($_POST['collect']) ? $_POST['collect'] : '';
    $prixAchat = isset($_POST['prixAchat']) ? $_POST['prixAchat'] : '';
    $prixAtout = isset($_POST['prixAtout']) ? $_POST['prixAtout'] : '';
    $prixStella = isset($_POST['prixStella']) ? $_POST['prixStella'] : '';
    $categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';
    $matiere = isset($_POST['matiere']) ? $_POST['matiere'] : '';

    if (!empty($reference) && !empty($collection) && !empty($nomProduit) && !empty($prixAchat) && !empty($prixAtout) && !empty($prixStella) && !empty($categorie)  && !empty($matiere)) {
        $sqlDeleteEmpty = "DELETE FROM produits WHERE nom = ''";
        $conn->query($sqlDeleteEmpty);

        $nomProduitEscaped = mysqli_real_escape_string($conn, $nomProduit);
        $sql = "INSERT INTO produits (nom, reference, collect, prixAchat, prixAtout, prixStella, categorie, codeBarre, matiere) VALUES ('$nomProduitEscaped', '$reference', '$collection', '$prixAchat', '$prixAtout', '$prixStella', '$categorie', '$nouveauNombreComplet', '$matiere')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<div class='affirm'><h1> Réussi<h1></div>";
        } else {
            echo "<div class='affirm'><h1> Erreur lors de l'enregistrement du nouveau produit : " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='affirm'><h1> Erreur lors de l'enregistrement du nouveau produit : " . $conn->error . "</div>";
    }
}


$conn->close();
?>

<html>
<head>
    <title>Générateur barcode</title>
    <link href="public/css/index.css" rel="stylesheet">
    <script type="text/javascript" src="public/js/index.js"></script>
</head>
<body>
<div class="main">
    <div class="menu">
        <h1>Odoo</h1>
        <hr>
        <div class="menu1">
            <span class="material-symbols-outlined"></span>
            <a href="index">Créer article</a>
            <hr>
            <div class="aa">
                <a href="categorie">Categorie</a>
                <a href="collection">Collection</a>
                <a href="matiere">Matière</a>
                <a href="nouvellecollection">Dernier EAN</a>
            </div>
        </div>
    </div>
    <div class="droite">

    <div class="export">
    <form action="" method="post">
        <img src="public/img/export.png">
        <input type="submit" name="exporter" value="Export">
    </form>
    </div>

            <form action="index.php" method="post">
            <div class="formulaire">
                <div>
                    <label for="nom">Nom du produit :</label>
                    <input type="text" name="nom" id="nom" required>
                </div>
                <div>
                    <label for="reference">Référence :</label>
                    <input type="text" name="reference" id="reference" required>
                </div>
                <div>
                    <label for="collect">Collection :</label>
                    <select name="collect" id="collect">
                        <?php
                        foreach ($collections as $collection) {
                            echo "<option value='$collection'>$collection</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="prixAchat">Prix d'achat :</label>
                    <input type="text" name="prixAchat" id="prixAchat" required>
                </div>
                <div>
                    <label for="prixAtout">Prix Atout :</label>
                    <input type="text" name="prixAtout" id="prixAtout" required>
                </div>
                <div>
                    <label for="prixStella">Prix Stella :</label>
                    <input type="text" name="prixStella" id="prixStella" required>
                </div>
                <div>
                    <label for="categorie">Catégorie :</label>
                    <select name="categorie" id="categorie">
                        <?php
                        foreach ($categories as $categorie) {
                            echo "<option value='$categorie'>$categorie</option>";
                        }
                        ?>
                    </select>
                </div>
                            
                <div>
                    <label for="matiere">Matière :</label>
                    <select name="matiere" id="matiere">
                        <?php
                        foreach ($matieres as $matiere) {
                            echo "<option value='$matiere'>$matiere</option>";
                        }
                        ?>
                    </select>
                </div>
              
                </div>
                <div>
                    <input type="submit" value="Créer">

                </div>
            </form>
            <div>

</div>

 
</div>
</div>
 
</body>
</html>
