<?php

// La classe Electronic hérite de la classe Product
class Electronic extends Product {
    
    
    private ?string $brand;
    private ?int $warranty_fee;

    public function __construct(
        ?int $id = null,
        ?string $name = null,
        array $photos = [], 
        ?int $price = null,
        ?string $description = null,
        ?int $quantity = null,
        DateTime $createdAt = new DateTime(), 
        DateTime $updatedAt = new DateTime(), 
        ?int $category_id = null,
        // propriétés spécifiques à la class Electronic
        ?string $brand = null,
        ?int $warranty_fee = null
    ) {
        parent::__construct(
            $id, $name, $photos, $price, $description, $quantity,
            $createdAt, $updatedAt, $category_id
        );
        
        $this->brand = $brand;
        $this->warranty_fee = $warranty_fee;
    }

   
    // Surcharge de CREATE() 
    
    public function create(): Electronic|false {
        
        $parent_result = parent::create();

        if ($parent_result === false) {
            return false;
        }

        $pdo = Product::getPdo(); 
        if ($pdo === null) {
            return false;
        }

        
        $sql = "INSERT INTO electronique 
                (product_id, brand, warranty_fee) 
                VALUES 
                (:product_id, :brand, :warranty_fee)";
        
        try {
            $stmt = $pdo->prepare($sql);
            
            $success = $stmt->execute([
                'product_id'   => $this->getId(),
                'brand'        => $this->brand,
                'warranty_fee' => $this->warranty_fee,
            ]);
            
            return $success ? $this : false;
        } catch (\PDOException $e) {
            error_log("Erreur d'insertion electronique : " . $e->getMessage());
            return false;
        }
    }

    
    public function update(): Electronic|false { 
       
        if ($this->getId() === null) { 
            return false;
        }
        
        // Mise à jour du parent (table 'product')
        $parent_result = parent::update();
        if ($parent_result === false) {
            return false;
        }

        $pdo = Product::getPdo();
        if ($pdo === null) {
            return false;
        }
        
        
        $sql = "UPDATE electronique SET 
                brand = :brand, 
                warranty_fee = :warranty_fee 
                WHERE product_id = :product_id"; 
        
        try {
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                'product_id'   => $this->getId(),
                'brand'        => $this->brand,
                'warranty_fee' => $this->warranty_fee,
            ]);
            
            return $this; 
        } catch (\PDOException $e) {
            error_log("Erreur de mise à jour electronique : " . $e->getMessage());
            return false;
        }
    }

    
    public function findOneById(int $id): Electronic|false {
        $pdo = Product::getPdo();
        if ($pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }

        $sql = "SELECT p.*, e.brand, e.warranty_fee
                FROM product p
                JOIN electronique e ON p.id = e.product_id
                WHERE p.id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $combined_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$combined_row) {
            return false;
        }

        $tempProduct = Product::fromDatabaseRow($combined_row);
        
        $this->setId($tempProduct->getId() ?? 0); 
        $this->setName($tempProduct->getName());
        $this->setPhotos($tempProduct->getPhotos());
        $this->setPrice($tempProduct->getPrice() ?? 0);
        $this->setDescription($tempProduct->getDescription());
        $this->setQuantity($tempProduct->getQuantity() ?? 0);
        $this->setCreatedAt($tempProduct->getCreatedAt());
        $this->setUpdatedAt($tempProduct->getUpdatedAt());
        $this->setCategoryId($tempProduct->getCategoryId() ?? 0);
        
        $this->setBrand($combined_row['brand']);
        $this->setWarrantyFee((int) $combined_row['warranty_fee']); 
        
        return $this; 
    }

    
    public static function findAll(): array {
        $pdo = Product::getPdo();
        if ($pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }

        $electronic_list = [];
        
        $sql = "SELECT p.*, e.brand, e.warranty_fee
                FROM product p
                JOIN electronique e ON p.id = e.product_id";
                
        $stmt = $pdo->query($sql);
        
        while ($combined_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $electronic = new Electronic (
                (int) $combined_row['id'],
                $combined_row['name'],
                explode(',', $combined_row['photos']), 
                (int) $combined_row['price'],
                $combined_row['description'],
                (int) $combined_row['quantity'],
                new DateTime($combined_row['createdAt']),
                new DateTime($combined_row['updatedAt']),
                (int) $combined_row['category_id'],
                
                $combined_row['brand'],
                (int) $combined_row['warranty_fee'] 
            );
            
            $electronic_list[] = $electronic;
        }

        return $electronic_list;
    }

    
    public function getBrand(): ?string { return $this->brand; }
    public function getWarrantyFee(): ?int { return $this->warranty_fee; }

    public function setBrand(?string $brand): void { $this->brand = $brand; }
    public function setWarrantyFee(?int $warranty_fee): void { $this->warranty_fee = $warranty_fee; }
}
?>