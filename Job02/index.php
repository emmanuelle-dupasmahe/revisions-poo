<?php
require "product.php";
require "category.php";


$cat_vetements = new Category(
    10,
    "Vêtements",
    "Tous les articles textiles.",
    new DateTime('2025-11-01 09:00:00'),
    new DateTime('2025-11-01 09:00:00')
);

echo "<h2>Catégorie Créée</h2>";
var_dump($cat_vetements);


// Création d'un Produit
$product2 = new Product(
    2,
    "Jean Slim",
    ["jean_devant.jpg", "jean_derriere.jpg"],
    5999,
    "Un jean coupe slim confortable et résistant.",
    20,
    new DateTime('2025-11-20 12:00:00'),
    new DateTime('2025-11-20 12:00:00'),
    $cat_vetements->getId() // c'est (10) voir plus haut
);

echo "<h2>2. Produit Associé à la Catégorie</h2>";
var_dump($product2);




echo "<h2>3. Vérification du category_id</h2>";
echo "ID de la Catégorie pour le produit : " . $product2->getCategoryId() . "<br>";

echo "Catégorie associée (via l'ID) : " . $cat_vetements->getName() . " (ID: " . $cat_vetements->getId() . ")<br>";
?>