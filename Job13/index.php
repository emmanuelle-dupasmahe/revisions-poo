<?php

require_once 'Product.php';
require_once 'Category.php';


//connexion BDD

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
     echo "✅ Connexion à la base de données réussie.<br>";
} catch (\PDOException $e) {
     die("❌ Erreur de connexion : " . $e->getMessage());
}


// injection de la dépendance PDO

Product::setPdo($pdo);
Category::setPdo($pdo); 
echo "✅ Connexion PDO injectée dans Product et Category.<br><br>";



// création et insertion
echo "<h2>Création et insertion d'un nouveau produit (create)</h2>";

$new_product = new Product();
$new_product->setName("Clé USB 128Go");
$new_product->setPrice(1599);
$new_product->setDescription("Clé USB 3.0 ultra-rapide.");
$new_product->setQuantity(500);
$new_product->setPhotos(['usb_1.jpg', 'usb_2.jpg']);
$new_product->setCategoryId(3); 

echo "Tentative d'insertion du produit :  " . $new_product->getName() . " (ID actuel: " . ($new_product->getId() ?? 'NULL') . ")<br>";

// Appel de la méthode create()
$result_create = $new_product->create();

if ($result_create !== false) {
    echo "✅ Insertion réussie ! <br>";
    echo "ID généré par la base de données : " . $new_product->getId() . "<br>";
    echo "Vérification : l'objet est maintenant hydraté avec l'ID.<br>";
    var_dump($new_product);
} else {
    echo "❌ Échec de l'insertion du produit.";
}

echo "<hr>";


// récupération de tous les produits
echo "<h2>Récupération de tous les produits (findAll)</h2>";

try {
    $all_products = Product::findAll();

    if (empty($all_products)) {
        echo "❌ Aucune donnée dans la table 'product'.";
    } else {
        echo "✅ " . count($all_products) . " produits ont été récupérés et hydratés.<br>";

        echo "<h3>Liste des produits hydratés :</h3>";
        
        foreach ($all_products as $product) {
            echo "Produit ID " . $product->getId() . " : " . $product->getName() . " (Prix: " . number_format($product->getPrice() / 100, 2) . " €)<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de l'appel de findAll() : " . $e->getMessage();
}

echo "<hr>";


// recherche  (findOneById + getCategory) 
$product_id_to_find = 7;
echo "<h2>Recherche du produit ID {$product_id_to_find} (findOneById)</h2>";

$product7 = new Product(); 
$result = $product7->findOneById($product_id_to_find);

if ($result !== false) {
    echo "✅ Produit ID {$product_id_to_find} trouvé et instance hydratée.<br>";
    echo "Nom du produit : " . $product7->getName() . "**<br>";
    
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

echo "<hr>";


// Récupération des prdoduits d'une catégorie
$category_id_to_fetch = 1;

echo "<h2>Récupération des produits de la catégorie ID {$category_id_to_fetch} (getProducts)</h2>";

// Récupérer la Catégorie (Hydratation Manuelle)
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
        echo "Produit ID **" . $product->getId() . "** : " . $product->getName() . " (Stock: " . $product->getQuantity() . ")<br>";
    }
}
?>