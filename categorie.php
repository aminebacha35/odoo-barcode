<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "odoo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

define("CAT_PER_PAGE", 6); 

function createCategorie($nom)
{
    global $conn;
    $nom = $conn->real_escape_string($nom);

    $sql = "INSERT INTO categories (nom) VALUES ('$nom')";
    $result = $conn->query($sql);

    if ($result) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

function getCategories($offset, $limit)
{
    global $conn;

    $sql = "SELECT * FROM categories LIMIT $offset, $limit";
    $result = $conn->query($sql);

    $categories = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    return $categories;
}

function updateCategorie($id, $nom)
{
    global $conn;
    $id = (int)$id;
    $nom = $conn->real_escape_string($nom);

    $sql = "UPDATE categories SET nom = '$nom' WHERE id = $id";
    $result = $conn->query($sql);

    return $result;
}

function deleteCategorie($id)
{
    global $conn;
    $id = (int)$id;

    $sql = "DELETE FROM categories WHERE id = $id";
    $result = $conn->query($sql);

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCategorie'])) {
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';

    if (!empty($nom)) {
        $categorieId = createCategorie($nom);
        header('Location: categorie');
        exit();
    }
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * CAT_PER_PAGE;

$categories = getCategories($offset, CAT_PER_PAGE);

$totalCategories = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
$totalPages = ceil($totalCategories / CAT_PER_PAGE);

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
        echo '<h2>Créer une categorie</h2>';
        echo '<div class="titleCrud"><form action="" method="post">';
        echo '<input type="text" name="nom" placeholder="Nom de la categorie" required>';
        echo '<input type="submit" name="createCategorie" value="+">';
        echo '</form> </div>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['updateCategorie'])) {
                $id = $_POST['categorieId'];
                $nom = $_POST['nom'];

                $updateResult = updateCategorie($id, $nom);
                header('Location: categorie');
                exit();

            }

            if (isset($_POST['deleteCategorie'])) {
                $id = $_POST['categorieId'];

                $deleteResult = deleteCategorie($id);
                header('Location: categorie');
                exit();
            }
        }

        if (!empty($categories)) {
            echo '<h2>Liste des categories</h2>';
            echo '<div class="ull">';
            echo '<ul>';

            foreach ($categories as $categorie) {
                echo '<p>';
                echo '<form action="" method="post" style="display: inline-block;">';
                echo '<input type="hidden" name="categorieId" value="' . $categorie['id'] . '">';
                echo '<input type="text" name="nom" value="' . htmlspecialchars($categorie['nom']) . '" required>';
                echo '<input type="submit" name="updateCategorie" value="Modifier">';
                echo '</form>';
                echo '<form action="" method="post" style="display: inline-block;">';
                echo '<input type="hidden" name="categorieId" value="' . $categorie['id'] . '">';
                echo '<input type="submit" name="deleteCategorie" value="Supprimer">';
                echo '</form>';
                echo '</p>';
            }

            echo '</ul>';
            echo '</div>';

            if ($totalPages > 1) {
                echo '<div class="pagination">';

                for ($i = 1; $i <= $totalPages; $i++) {
                    $active = ($i === $page) ? 'active' : '';
                    echo '<a class="' . $active . '" href="categorie?page=' . $i . '">' . $i . '</a>';
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
