<?php

require 'Product.php';

$product1 = new Product(
    1,
    "T-shirt en Coton",
    ["tshirt_bleu.jpg", "tshirt_porte.jpg"],
    2500, 
    "Un t-shirt 100% coton.",
    50,
    new DateTime('2025-01-15 10:00:00'),
    new DateTime('2025-01-15 10:00:00')
);

echo "<h2>État Initial du Produit</h2>";
var_dump($product1);


// Utilisation des Getters pour vérifier les valeurs
echo "<h2>Vérification avec les Getters</h2>";
echo "ID du produit : " . $product1->getId() . "<br>";
echo "Nom du produit : " . $product1->getName() . "<br>";
echo "Quantité en stock : " . $product1->getQuantity() . "<br>";
echo "Date de création : " . $product1->getCreatedAt()->format('Y-m-d H:i:s') . "<br>";



// Utilisation des Setters pour modifier les valeurs
echo "<h2>Modification avec les Setters</h2>";

// Modification du prix et de la quantité
$product1->setPrice(2299); // maintenant 22.99 € au lieu de 25 €
$product1->setQuantity($product1->getQuantity() - 1); // Vente d'un tee shirt
$product1->setUpdatedAt(new DateTime()); // Mise à jour de la date de modification

echo "Prix mis à jour : " . $product1->getPrice() . "<br>";
echo "Nouvelle quantité : " . $product1->getQuantity() . "<br>";
echo "Date de modification : " . $product1->getUpdatedAt()->format('Y-m-d H:i:s') . "<br>";



// Vérification finale
echo "<h2>4. État Final du Produit</h2>";
var_dump($product1);
?>