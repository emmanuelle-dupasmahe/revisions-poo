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


Product::setPdo($pdo);
Category::setPdo($pdo); 
echo "✅ Connexion PDO injectée dans Product et Category.<br><br>";



$category_id_to_fetch = 1;
echo "<h2>Récupération de la Catégorie ID {$category_id_to_fetch}</h2>";

$stmt_cat = $pdo->prepare("SELECT * FROM category WHERE id = :id");
$stmt_cat->execute(['id' => $category_id_to_fetch]);
$category_row = $stmt_cat->fetch(PDO::FETCH_ASSOC);

if (!$category_row) {
    die("❌ Catégorie non trouvée.");
}

// Hydratation de l'objet Category
$category_instance = new Category(
    (int) $category_row['id'],
    $category_row['name'],
    $category_row['description'],
    new DateTime($category_row['createdAt']),
    new DateTime($category_row['updatedAt'])
);

echo "Catégorie récupérée : " . $category_instance->getName() . "<br>";




echo "<h2>Récupération des Produits Associés</h2>";

$products_list = $category_instance->getProducts();

if (empty($products_list)) {
    echo "❌ Aucun produit trouvé pour cette catégorie.";
} else {
    echo "✅ " . count($products_list) . " produits trouvés pour la catégorie '{$category_instance->getName()}'.<br><br>";

    echo "<h3>Détails des produits :</h3>";
    
    // Parcourir le tableau des objets Product
    foreach ($products_list as $product) {
        // Chaque élément est une instance de la classe Product !
        echo "Produit ID " . $product->getId() . " : " . $product->getName() . " (Stock: " . $product->getQuantity() . ")<br>";
    }
    
    echo "<br>Vérification du type du premier élément : ";
    var_dump($products_list[0]);
}

?>