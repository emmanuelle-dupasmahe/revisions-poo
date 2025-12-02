<?php


// La classe Clothing est une enfant de la classe Product
class Clothing extends Product {
    
    private ?string $size;
    private ?string $color;
    private ?string $type;
    private ?int $material_fee;

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
        // propriétés qui ne sont que pour clothing
        ?string $size = null,
        ?string $color = null,
        ?string $type = null,
        ?int $material_fee = null
    ) {
        // Appelle le constructeur de la classe mère (Product)
        parent::__construct(
            $id,
            $name,
            $photos,
            $price,
            $description,
            $quantity,
            $createdAt,
            $updatedAt,
            $category_id
        );
        
        // Initialisation des propriétés spécifiques
        $this->size = $size;
        $this->color = $color;
        $this->type = $type;
        $this->material_fee = $material_fee;
    }
    
    public function create(): Clothing|false {
        // 1. Insertion dans la table 'product' (Parent)
        $parent_result = parent::create();

        if ($parent_result === false) {
            return false;
        }

        $pdo = Product::getPdo(); 
        if ($pdo === null) {
            return false;
        }

        // Insertion dans la table de détails 'clothing'
        $sql = "INSERT INTO clothing 
                (product_id, size, color, type, material_fee) 
                VALUES 
                (:product_id, :size, :color, :type, :material_fee)";
        
        try {
            $stmt = $pdo->prepare($sql);
            
            $success = $stmt->execute([
                'product_id'   => $this->getId(),
                'size'         => $this->size,
                'color'        => $this->color,
                'type'         => $this->type,
                'material_fee' => $this->material_fee,
            ]);
            
            return $success ? $this : false;
        } catch (\PDOException $e) {
            error_log("Erreur d'insertion clothing : " . $e->getMessage());
            
            return false;
        }
    }

   
    public function update(): Clothing|false { 
        if ($this->id === null) {
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
        
        // Mise à jour de la table 'clothing'
        $sql = "UPDATE clothing SET 
                size = :size, 
                color = :color, 
                type = :type, 
                material_fee = :material_fee
                WHERE product_id = :product_id"; 
        
        try {
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                'product_id'   => $this->getId(),
                'size'         => $this->size,
                'color'        => $this->color,
                'type'         => $this->type,
                'material_fee' => $this->material_fee,
            ]);
            
            return $this; 
        } catch (\PDOException $e) {
            error_log("Erreur de mise à jour clothing : " . $e->getMessage());
            return false;
        }
    }

    
    public function findOneById(int $id): Clothing|false {
        $pdo = Product::getPdo();
        if ($pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }

        // Requête utilisant une jointure pour récupérer toutes les données en une seule fois
        $sql = "SELECT p.*, c.size, c.color, c.type, c.material_fee
                FROM product p
                JOIN clothing c ON p.id = c.product_id
                WHERE p.id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $combined_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$combined_row) {
            return false;
        }

        // Hydratation de l'instance en cours ($this)
        
        // Hydratation des propriétés du parent via l'objet temporaire
        $tempProduct = Product::fromDatabaseRow($combined_row);
        
        // Transfert des propriétés du parent
        $this->setId($tempProduct->getId() ?? 0); 
        $this->setName($tempProduct->getName());
        $this->setPhotos($tempProduct->getPhotos());
        $this->setPrice($tempProduct->getPrice() ?? 0);
        $this->setDescription($tempProduct->getDescription());
        $this->setQuantity($tempProduct->getQuantity() ?? 0);
        $this->setCreatedAt($tempProduct->getCreatedAt());
        $this->setUpdatedAt($tempProduct->getUpdatedAt());
        $this->setCategoryId($tempProduct->getCategoryId() ?? 0);
        
        // Transfert des propriétés spécifiques (Clothing)
        $this->setSize($combined_row['size']);
        $this->setColor($combined_row['color']);
        $this->setType($combined_row['type']);
        $this->setMaterialFee((int) $combined_row['material_fee']);

        return $this; 
    }

    public static function findAll(): array {
        $pdo = Product::getPdo();
        if ($pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }

        $clothing_list = [];
        
        // Requête pour joindre les données du parent et de l'enfant
        $sql = "SELECT p.*, c.size, c.color, c.type, c.material_fee
                FROM product p
                JOIN clothing c ON p.id = c.product_id";
                
        $stmt = $pdo->query($sql);
        
        while ($combined_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            // Crée et hydrate une nouvelle instance de Clothing
            $clothing = new Clothing(
                (int) $combined_row['id'],
                $combined_row['name'],
                explode(',', $combined_row['photos']), 
                (int) $combined_row['price'],
                $combined_row['description'],
                (int) $combined_row['quantity'],
                new DateTime($combined_row['createdAt']),
                new DateTime($combined_row['updatedAt']),
                (int) $combined_row['category_id'],
                // Propriétés spécifiques
                $combined_row['size'],
                $combined_row['color'],
                $combined_row['type'],
                (int) $combined_row['material_fee']
            );
            
            $clothing_list[] = $clothing;
        }

        return $clothing_list;
    }

    // Getters 
    public function getSize(): ?string { return $this->size; }
    public function getColor(): ?string { return $this->color; }
    public function getType(): ?string { return $this->type; }
    public function getMaterialFee(): ?int { return $this->material_fee; }

    // Setters 
    public function setSize(?string $size): void { $this->size = $size; }
    public function setColor(?string $color): void { $this->color = $color; }
    public function setType(?string $type): void { $this->type = $type; }
    public function setMaterialFee(?int $material_fee): void { $this->material_fee = $material_fee; }
}
?>