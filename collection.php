<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "odoo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

define("COL_PER_PAGE", 6);

function createCollection($nom)
{
    global $conn;
    $nom = $conn->real_escape_string($nom);

    $sql = "INSERT INTO collections (nom) VALUES ('$nom')";
    $result = $conn->query($sql);

    if ($result) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

function getCollections($offset, $limit)
{
    global $conn;

    $sql = "SELECT * FROM collections LIMIT $offset, $limit";
    $result = $conn->query($sql);

    $collections = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $collections[] = $row;
        }
    }

    return $collections;
}

function updateCollection($id, $nom)
{
    global $conn;
    $id = (int)$id;
    $nom = $conn->real_escape_string($nom);

    $sql = "UPDATE collections SET nom = '$nom' WHERE id = $id";
    $result = $conn->query($sql);

    return $result;
}

function deleteCollection($id)
{
    global $conn;
    $id = (int)$id;

    $sql = "DELETE FROM collections WHERE id = $id";
    $result = $conn->query($sql);

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCollection'])) {
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';

    if (!empty($nom)) {
        $collectionId = createCollection($nom);
        header('Location: collection');
        exit();
    }
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * COL_PER_PAGE;

$collections = getCollections($offset, COL_PER_PAGE);

$totalCollections = $conn->query("SELECT COUNT(*) as total FROM collections")->fetch_assoc()['total'];
$totalPages = ceil($totalCollections / COL_PER_PAGE);

?>

<html>
<head>
    <title>Gestion des données</title>
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
        <?php
        echo '<h2>Créer une collection</h2>';
        echo '<div class="titleCrud"><form action="" method="post">';
        echo '<input type="text" name="nom" placeholder="Nom de la collection" required>';
        echo '<input type="submit" name="createCollection" value="+">';
        echo '</form></div>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['updateCollection'])) {
                $id = $_POST['collectionId'];
                $nom = $_POST['nom'];

                $updateResult = updateCollection($id, $nom);
                header('Location: collection');
                exit();

            }

            if (isset($_POST['deleteCollection'])) {
                $id = $_POST['collectionId'];

                $deleteResult = deleteCollection($id);
                header('Location: collection');
                exit();
            }
        }

        if (!empty($collections)) {
            echo '<h2>Liste des collections</h2>';
            echo '<div class="ull">';
            echo '<ul>';

            foreach ($collections as $collection) {
                echo '<p>';
                echo '<form action="" method="post" style="display: inline-block;">';
                echo '<input type="hidden" name="collectionId" value="' . $collection['id'] . '">';
                echo '<input type="text" name="nom" value="' . htmlspecialchars($collection['nom']) . '" required>';
                echo '<input type="submit" name="updateCollection" value="Modifier">';
                echo '</form>';
                echo '<form action="" method="post" style="display: inline-block;">';
                echo '<input type="hidden" name="collectionId" value="' . $collection['id'] . '">';
                echo '<input type="submit" name="deleteCollection" value="Supprimer">';
                echo '</form>';
                echo '</p>';
            }

            echo '</ul>';
            echo '</div>';

            if ($totalPages > 1) {
                echo '<div class="pagination">';

                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i === $page) ? 'active' : '';
                    echo '<a class="' . $active . '" href="collection?page=' . $i . '">' . $i . '</a>';
                }

                echo '</div>';
            }
        } else {
            echo '<h2>Aucune collection trouvée.</h2>';
        }
        ?>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
