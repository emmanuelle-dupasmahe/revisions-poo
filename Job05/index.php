<?php
require_once 'product.php';
require_once 'category.php';

$host = 'localhost';
$db   = 'draft-shop'; 
$user = 'root';             
$pass = '';            
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "✅ Connexion à la base de données réussie.<br><br>";
} catch (\PDOException $e) {
     die("Erreur de connexion : " . $e->getMessage());
}

$product_id_7 = 7;
echo "Tentative de récupération du produit avec l'ID : {$product_id_7}<br>";


$stmt = $pdo->prepare("SELECT * FROM product WHERE id = :id");
$stmt->execute(['id' => $product_id_7]);

$product_row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product_row) {
    die("❌ Aucun produit trouvé avec l'ID {$product_id_7}. Veuillez vérifier votre base de données.");
}

echo "Données SQL brutes récupérées : ";
var_dump($product_row);
echo "<br>";


Product::setPdo($pdo);
echo "✅ Connexion PDO injectée dans la classe Product.<br><br>";
// Hydratation de l'instance Product 


$product_instance = new Product();

$product_instance->setName($product_row['name']);
$product_instance->setPhotos(explode(',', $product_row['photos']));
$product_instance->setPrice((int) $product_row['price']);
$product_instance->setDescription($product_row['description']);
$product_instance->setQuantity((int) $product_row['quantity']);
$product_instance->setCreatedAt(new DateTime($product_row['createdAt']));
$product_instance->setUpdatedAt(new DateTime($product_row['updatedAt']));
$product_instance->setCategoryId((int) $product_row['category_id']);

echo "<h2> Produit hydraté :</h2>";
var_dump($product_instance);

echo "<br>Nom du produit hydraté : " . $product_instance->getName() . "";
echo "<br>Quantité en stock : " . $product_instance->getQuantity();
echo "<br>Prix : " . number_format($product_instance->getPrice() / 100, 2) . " €";

echo "<h2>Résolution de la Relation de Catégorie</h2>";

$category_instance = $product_instance->getCategory();

if ($category_instance) {
    echo "✅ Catégorie associée récupérée avec succès !<br>";
    echo "Nom de la Catégorie : " . $category_instance->getName() . " (ID: " . $category_instance->getId() . ")<br>";
    echo "Description : " . $category_instance->getDescription() . "<br>";
    
    echo "<br><h3>Détails de l'instance Category :</h3>";
    var_dump($category_instance);
} else {
    echo "❌ Impossible de trouver la catégorie associée (ID: " . $product_instance->getCategoryId() . ")";
}

?>