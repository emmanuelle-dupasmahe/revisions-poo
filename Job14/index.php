<?php


require_once 'AbstractProduct.php';
require_once 'Category.php';
require_once 'StockableInterface.php'; 
require_once 'Clothing.php'; 
require_once 'Electronic.php'; 


// Connexion BDD 

$host = 'localhost';
$db  = 'draft-shop'; 
$user = 'root'; 
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES     => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     echo "✅ Connexion à la base de données réussie.<br>";
} catch (\PDOException $e) {
     die("❌ Erreur de connexion : " . $e->getMessage());
}





AbstractProduct::setPdo($pdo);
Category::setPdo($pdo); 
echo "✅ Connexion PDO injectée dans AbstractProduct et Category.<br><br>";




$shirt = new Clothing(
    id: 101, 
    name: 'T-shirt Logo', 
    price: 2500, 
    quantity: 50, // Stock initial
    description: 'Un classique décontracté',
    category_id: 1,
    size: 'L',
    color: 'Bleu',
    type: 'Tee',
    material_fee: 500
);

echo "État Initial <br>";
echo "Article: " . $shirt->getName() . "<br>";
echo "Stock initial: " . $shirt->getQuantity() . " unités <br>";
echo "<br>";



echo "Test de addStocks <br>";
$stock_added = 25;
$shirt->addStocks($stock_added);

echo "Ajout de $stock_added unités. Nouveau stock: " . $shirt->getQuantity() . " unités\n";
echo "<br><br>";



echo "Test de removeStocks <br>";
$stock_removed = 10;
$shirt->removeStocks($stock_removed);

echo "Retrait de $stock_removed unités. Nouveau stock: " . $shirt->getQuantity() . " unités\n";
echo "<br><br>";



echo "Test de Sécurité (Retrait excessif)<br>";
$stock_actuel = $shirt->getQuantity();
$retrait_excessif = 100;

echo "Tentative de retirer $retrait_excessif unités (Stock actuel: $stock_actuel)\n";
$shirt->removeStocks($retrait_excessif);

echo "Stock final après retrait excessif: " . $shirt->getQuantity() . " unités\n";


echo "<br><br>";
echo "Test de Chaînage <br>";
$stock_chaining = $shirt->addStocks(5)
                        ->removeStocks(2)
                        ->addStocks(10);
                        
echo "Stock après chaînage (+5, -2, +10): " . $stock_chaining->getQuantity() . " unités\n";