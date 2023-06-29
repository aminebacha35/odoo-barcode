<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "odoo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

define("MAT_PER_PAGE", 6);

function createMatiere($nom)
{
    global $conn;
    $nom = $conn->real_escape_string($nom);

    $sql = "INSERT INTO matieres (nom) VALUES ('$nom')";
    $result = $conn->query($sql);

    if ($result) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

function getMatieres($offset, $limit)
{
    global $conn;

    $sql = "SELECT * FROM matieres LIMIT $offset, $limit";
    $result = $conn->query($sql);

    $matieres = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $matieres[] = $row;
        }
    }

    return $matieres;
}

function updateMatiere($id, $nom)
{
    global $conn;
    $id = (int)$id;
    $nom = $conn->real_escape_string($nom);

    $sql = "UPDATE matieres SET nom = '$nom' WHERE id = $id";
    $result = $conn->query($sql);

    return $result;
}

function deleteMatiere($id)
{
    global $conn;
    $id = (int)$id;

    $sql = "DELETE FROM matieres WHERE id = $id";
    $result = $conn->query($sql);

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createMatiere'])) {
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';

    if (!empty($nom)) {
        $matiereId = createMatiere($nom);
        header('Location: matiere');
        exit();
    }
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * MAT_PER_PAGE;

$matieres = getMatieres($offset, MAT_PER_PAGE);

$totalMatieres = $conn->query("SELECT COUNT(*) as total FROM matieres")->fetch_assoc()['total'];
$totalPages = ceil($totalMatieres / MAT_PER_PAGE);

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
        echo '<h2>Créer une matière</h2>';
        echo '<div class="titleCrud"><form action="" method="post">';
        echo '<input type="text" name="nom" placeholder="Nom de la matière" required>';
        echo '<input type="submit" name="createMatiere" value="+">';
        echo '</form></div>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['updateMatiere'])) {
                $id = $_POST['matiereId'];
                $nom = $_POST['nom'];

                $updateResult = updateMatiere($id, $nom);
                header('Location: matiere');
                exit();

            }

            if (isset($_POST['deleteMatiere'])) {
                $id = $_POST['matiereId'];

                $deleteResult = deleteMatiere($id);
                header('Location: matiere');
                exit();
            }
        }

        if (!empty($matieres)) {
            echo '<h2>Liste des matières</h2>';
            echo '<div class="ull">';
            echo '<ul>';

            foreach ($matieres as $matiere) {
                echo '<p>';
                echo '<form action="" method="post" style="display: inline-block;">';
                echo '<input type="hidden" name="matiereId" value="' . $matiere['id'] . '">';
                echo '<input type="text" name="nom" value="' . htmlspecialchars($matiere['nom']) . '" required>';
                echo '<input type="submit" name="updateMatiere" value="Modifier">';
                echo '</form>';
                echo '<form action="" method="post" style="display: inline-block;">';
                echo '<input type="hidden" name="matiereId" value="' . $matiere['id'] . '">';
                echo '<input type="submit" name="deleteMatiere" value="Supprimer">';
                echo '</form>';
                echo '</p>';
            }

            echo '</ul>';
            echo '</div>';

            if ($totalPages > 1) {
                echo '<div class="pagination">';

                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i === $page) ? 'active' : '';
                    echo '<a class="' . $active . '" href="matiere?page=' . $i . '">' . $i . '</a>';
                }

                echo '</div>';
            }
        } else {
            echo '<h2>Aucune matière trouvée.</h2>';
        }
        ?>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>
