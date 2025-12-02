<?php

abstract class AbstractProduct {
    //changement de ma classe Product en classe AbstractProduct
    
    protected ?int $id;
    protected ?string $name;
    protected array $photos; 
    protected ?int $price;
    protected ?string $description;
    protected ?int $quantity;
    protected DateTime $createdAt;
    protected DateTime $updatedAt;
    protected ?int $category_id;

    // Propriété statique pour la connexion PDO
    private static ?PDO $pdo = null;


    /**
     * Constructeur de la classe AbstractProduct (avec tous les paramètres optionnels).
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

    public static function getPdo(): ?PDO {
    return self::$pdo;
    }
    
    // --- Méthode d'Hydratation (Nécessaire pour findOneById) ---
    /**
     * Crée un objet Product à partir d'une ligne de base de données.
     */
    public static function fromDatabaseRow(array $row): static{
        // Utilise 'new static' pour instancier la classe enfant (Clothing/Electronic)
        return new static(
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
    
    abstract public function create(): AbstractProduct|false;

    abstract public function update(): AbstractProduct|false;

    abstract public function findOneById(int $id): AbstractProduct|false;

    abstract public static function findAll(): array;

    protected function createProduct(): AbstractProduct|false {
        if (self::$pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée.");
        }
        
        if ($this->name === null || $this->price === null || $this->category_id === null) {
            return false;
        }

        $sql = "INSERT INTO product 
                (category_id, name, photos, price, description, quantity, createdAt, updatedAt) 
                VALUES 
                (:category_id, :name, :photos, :price, :description, :quantity, NOW(), NOW())";
        
        try {
            $stmt = self::$pdo->prepare($sql);
            
            $success = $stmt->execute([
                'category_id' => $this->category_id,
                'name'        => $this->name,
                'photos'      => implode(',', $this->photos), 
                'price'       => $this->price,
                'description' => $this->description,
                'quantity'    => $this->quantity,
            ]);

            if ($success) {
                $new_id = self::$pdo->lastInsertId();
                $this->id = (int) $new_id;
                return $this; 
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log("Erreur d'insertion de produit : " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour la partie "Product" dans la DB. Utilisé par les méthodes update() des classes enfants.
     */
    protected function updateProduct(): AbstractProduct|false {
        if ($this->id === null || self::$pdo === null) {
            return false;
        }
        
        $sql = "UPDATE product SET 
                category_id = :category_id, 
                name = :name, 
                photos = :photos, 
                price = :price, 
                description = :description, 
                quantity = :quantity,
                updatedAt = NOW() 
                WHERE id = :id";
        
        try {
            $stmt = self::$pdo->prepare($sql);
            
            $success = $stmt->execute([
                'id'          => $this->id,
                'category_id' => $this->category_id,
                'name'        => $this->name,
                'photos'      => implode(',', $this->photos), 
                'price'       => $this->price,
                'description' => $this->description,
                'quantity'    => $this->quantity,
            ]);

            if ($success) {
                $this->updatedAt = new DateTime();
                return $this; 
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            error_log("Erreur de mise à jour de produit ID {$this->id} : " . $e->getMessage());
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