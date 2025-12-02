<?php

class Product {
    
    
    private ?int $id;
    private ?string $name;
    private array $photos; 
    private ?int $price;
    private ?string $description;
    private ?int $quantity;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private ?int $category_id;

    // Propriété statique pour la connexion PDO
    private static ?PDO $pdo = null;


    /**
     * Constructeur de la classe Product (avec tous les paramètres optionnels).
     */
    public function __construct(
        ?int $id = null,
        ?string $name = null,
        array $photos = [], 
        ?int $price = null,
        ?string $description = null,
        ?int $quantity = null,
        DateTime $createdAt = new DateTime(), 
        DateTime $updatedAt = new DateTime(), 
        ?int $category_id = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->category_id = $category_id;
    }

    /**
     * Setter statique pour injecter la connexion PDO.
     * @param PDO $pdo L'objet de connexion à la base de données.
     */
    public static function setPdo(PDO $pdo): void {
        self::$pdo = $pdo;
    }
    
    // --- Méthode d'Hydratation (Nécessaire pour findOneById) ---
    /**
     * Crée un objet Product à partir d'une ligne de base de données.
     */
    public static function fromDatabaseRow(array $row): Product {
        // Hydratation complète
        return new Product(
            (int) $row['id'],
            $row['name'],
            explode(',', $row['photos']), 
            (int) $row['price'],
            $row['description'],
            (int) $row['quantity'],
            new DateTime($row['createdAt']),
            new DateTime($row['updatedAt']),
            (int) $row['category_id']
        );
    }
    
    // --- Méthode findOneById Corrigée ---
    /**
     * Recherche un produit par ID dans la base de données et hydrate l'instance en cours.
     * @param int $id L'ID du produit à rechercher.
     * @return Product|false L'instance Product hydratée ou false si l'ID n'est pas trouvé.
     */
    public function findOneById(int $id): Product|false {
        if (self::$pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }

        $stmt = self::$pdo->prepare("SELECT * FROM product WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        $product_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product_row) {
            return false; 
        }
        
        
        
        
        $tempProduct = self::fromDatabaseRow($product_row);
        
       
        $this->setId($tempProduct->getId() ?? 0); 
        $this->setName($tempProduct->getName());
        $this->setPhotos($tempProduct->getPhotos());
        $this->setPrice($tempProduct->getPrice() ?? 0);
        $this->setDescription($tempProduct->getDescription());
        $this->setQuantity($tempProduct->getQuantity() ?? 0);
        $this->setCreatedAt($tempProduct->getCreatedAt());
        $this->setUpdatedAt($tempProduct->getUpdatedAt());
        $this->setCategoryId($tempProduct->getCategoryId() ?? 0);
        
        return $this; 
    } 
    /**
     * Récupère TOUS les produits de la base de données.
     * Crée une instance de Product pour chaque ligne.
     * @return Product[] Tableau d'instances de la classe Product.
     */
    public static function findAll(): array {
        if (self::$pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }

        $products = [];
        
        // Requête pour récupérer toutes les lignes de la table 'product'
        $stmt = self::$pdo->query("SELECT * FROM product");
        
        while ($product_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = self::fromDatabaseRow($product_row);
        }

        return $products;
    }

    /**Pour créer un produit dans la base de données */
    
    public function create(): Product|false {
        if (self::$pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }
        
        // Assurez-vous que les champs non-nullables dans la DB sont définis
        if ($this->name === null || $this->price === null || $this->category_id === null) {
            return false;
        }

        $sql = "INSERT INTO product 
                (category_id, name, photos, price, description, quantity) 
                VALUES 
                (:category_id, :name, :photos, :price, :description, :quantity)";
        
        try {
            $stmt = self::$pdo->prepare($sql);
            
            // Exécution de la requête avec les données de l'instance
            $success = $stmt->execute([
                'category_id' => $this->category_id,
                'name'        => $this->name,
                // Les photos doivent être converties en chaîne de caractères pour la DB (séparées par des virgules)
                'photos'      => implode(',', $this->photos), 
                'price'       => $this->price,
                'description' => $this->description,
                'quantity'    => $this->quantity,
            ]);

            if ($success) {
                
                $new_id = self::$pdo->lastInsertId();
                $this->setId((int) $new_id);
                
        
                
                return $this; 
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            
            error_log("Erreur d'insertion de produit : " . $e->getMessage());
            return false;
        }
    }
    
    // les getters

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function getPhotos(): array { return $this->photos; }
    public function getPrice(): ?int { return $this->price; }
    public function getDescription(): ?string { return $this->description; }
    public function getQuantity(): ?int { return $this->quantity; }
    public function getCreatedAt(): DateTime { return $this->createdAt; }
    public function getUpdatedAt(): DateTime { return $this->updatedAt; }
    public function getCategoryId(): ?int { return $this->category_id; }


    //les setters

    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setPhotos(array $photos): void { $this->photos = $photos; }
    public function setPrice(int $price): void { if ($price >= 0) { $this->price = $price; } }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setQuantity(int $quantity): void { if ($quantity >= 0) { $this->quantity = $quantity; } }
    public function setCreatedAt(DateTime $createdAt): void { $this->createdAt = $createdAt; }
    public function setUpdatedAt(DateTime $updatedAt): void { $this->updatedAt = $updatedAt; }
    public function setCategoryId(int $category_id): void { $this->category_id = $category_id; }

    // --- Méthode getCategory() (inchangée) ---
    public function getCategory(): ?Category {
        if (self::$pdo === null) {
             throw new Exception("La connexion PDO n'a pas été initialisée pour la classe Product.");
        }
        if ($this->category_id === null) {
            return null;
        }
        $stmt = self::$pdo->prepare("SELECT * FROM category WHERE id = :id");
        $stmt->execute(['id' => $this->category_id]);
        $category_row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category_row) {
            return null;
        }
        
        
        return new Category(
            (int) $category_row['id'],
            $category_row['name'],
            $category_row['description'],
            new DateTime($category_row['createdAt']),
            new DateTime($category_row['updatedAt'])
        );
    } 
}
?>