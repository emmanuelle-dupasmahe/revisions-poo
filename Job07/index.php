<?php

require_once 'Product.php';
require_once 'Category.php';


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
     die("❌ Erreur de connexion : " . $e->getMessage());
}



Product::setPdo($pdo);
Category::setPdo($pdo); 
echo "✅ Connexion PDO injectée dans Product et Category.<br>";



//Recherche et Hydratation d'un Produit Spécifique (findOneById)

$product_id_to_find = 7;
echo "<h2>Recherche du produit ID {$product_id_to_find} </h2>";

// Créer une instance vide (grâce au constructeur optionnel)
$product7 = new Product(); 

// Hydrater l'instance avec findOneById()
$result = $product7->findOneById($product_id_to_find);

if ($result !== false) {
    echo "✅ Produit ID {$product_id_to_find} trouvé et instance hydratée.<br>";
    echo "Nom du produit : " . $product7->getName() . "<br>";
    echo "ID Catégorie stocké : " . $product7->getCategoryId() . "<br>";
    
    // Utilisation de la relation produit -> catégorie
    echo "<h3>Quelle est sa catégorie ?</h3>";
    $category_from_product = $product7->getCategory();
    
    if ($category_from_product) {
        echo "Catégorie associée : " . $category_from_product->getName() . "<br>";
    } else {
        echo "❌ Catégorie non trouvée pour ce produit.<br>";
    }
} else {
    echo "❌ Produit ID {$product_id_to_find} non trouvé dans la base de données.<br>";
}





//Recherche et Produits d'une Catégorie (getProducts)

$category_id_to_fetch = 1;

// Récupérer la Catégorie 
echo "<h2>Récupération des produits de la catégorie ID {$category_id_to_fetch}</h2>";

$stmt_cat = $pdo->prepare("SELECT * FROM category WHERE id = :id");
$stmt_cat->execute(['id' => $category_id_to_fetch]);
$category_row = $stmt_cat->fetch(PDO::FETCH_ASSOC);

if (!$category_row) {
    die("❌ Catégorie ID {$category_id_to_fetch} non trouvée.");
}

$category_instance = new Category(
    (int) $category_row['id'],
    $category_row['name'],
    $category_row['description'],
    new DateTime($category_row['createdAt']),
    new DateTime($category_row['updatedAt'])
);

echo "Catégorie source : " . $category_instance->getName() . "<br>";

// Appel de la méthode Category::getProducts()
$products_list = $category_instance->getProducts();

if (empty($products_list)) {
    echo "❌ Aucun produit trouvé pour cette catégorie.";
} else {
    echo "✅ " . count($products_list) . " produits trouvés.<br>";

    echo "<h3>Détails des produits trouvés :</h3>";
    
    foreach ($products_list as $product) {
        // Chaque élément est une instance de la classe Product !
        echo "Produit ID " . $product->getId() . " : " . $product->getName() . " (Stock: " . $product->getQuantity() . ")<br>";
    }
    
    echo "<br>Vérification du premier élément : ";
    var_dump($products_list[0]);
}
?>