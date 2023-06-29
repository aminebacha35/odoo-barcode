<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "odoo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codeBarre = isset($_POST['codeBarre']) ? $_POST['codeBarre'] : '';

    if (!empty($codeBarre)) {
        $sqlTransfer = "INSERT INTO ancienproduits SELECT * FROM produits";
        $conn->query($sqlTransfer);

        $sqlDelete = "DELETE FROM produits";
        $conn->query($sqlDelete);
        $sqlInsert = "INSERT INTO produits (codeBarre) VALUES ('$codeBarre')";
        $conn->query($sqlInsert);

        echo "<div class='affirm'><h1>Opération réussie</h1></div>";
    } else {
        echo "<div class='affirm'><h1>Veuillez saisir un code-barres</h1></div>";
    }
}

$conn->close();
?>

<html>
<head>
    <title>Nouvelle collection</title>
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
            <form action="nouvellecollection.php" method="post">
                <div class="">
                    <div class="">
                        <label for="codeBarre">Code-barres :</label>
                        <input type="text" name="codeBarre" id="codeBarre" required>
                    </div>
                </div>
                <div>
                    <input type="submit" value="Valider">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
