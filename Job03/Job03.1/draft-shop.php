<?php


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

// Connexion à la base de données 
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "Connexion à la base de données réussie !<br><br>";
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Insertion d'une nouvelle catégorie avec le code

$category_name = "Électronique";
$category_description = "Appareils, gadgets et accessoires numériques.";


$sql_category = "INSERT INTO category (name, description) VALUES (:name, :description)";
$stmt_category = $pdo->prepare($sql_category);

// Exécution avec les valeurs
$stmt_category->execute([
    'name' => $category_name,
    'description' => $category_description
]);

// Récupération de l'ID de la catégorie que nous venons d'insérer
$category_id = $pdo->lastInsertId();
echo "Catégorie '{$category_name}' insérée avec l'ID : **{$category_id}**<br>";

//Insertion d'un Produit 

$product_data = [
    'category_id' => $category_id,
    'name'        => "Écouteurs sans fil",
    'photos'      => "ecouteur.png",
    'price'       => 12900,
    'description' => "Écouteurs intra-auriculaires à réduction de bruit.",
    'quantity'    => 150
];

// Préparation de la requête
$sql_product = "
    INSERT INTO product 
    (category_id, name, photos, price, description, quantity) 
    VALUES 
    (:category_id, :name, :photos, :price, :description, :quantity)
";
$stmt_product = $pdo->prepare($sql_product);

// Exécution avec les données du produit
$stmt_product->execute($product_data);

$product_id = $pdo->lastInsertId();
echo "Produit '{$product_data['name']}' inséré avec l'ID : **{$product_id}**<br><br>";


echo "<h2>Vérification des insertions :</h2>";

$stmt_check = $pdo->query("SELECT p.name as product_name, c.name as category_name, p.price, p.quantity
                           FROM product p
                           JOIN category c ON p.category_id = c.id
                           WHERE p.id = {$product_id}");
$result = $stmt_check->fetch();

echo "Le produit {$result['product_name']} (Prix: {$result['price']} centimes, Stock: {$result['quantity']}) est bien associé à la catégorie {$result['category_name']}.";

?>