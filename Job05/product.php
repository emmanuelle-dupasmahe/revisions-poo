<?php

class Product {
    
    private ?int $id;//le ? indique que la valeur peut être Null
    private ?string $name;
    private array $photos; 
    private ?int $price;
    private ?string $description;
    private ?int $quantity;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private ?int $category_id;

    private static ?PDO $pdo = null;

    /**
     * Constructeur de la classe Product.
     * * @param int $id
     * @param string $name
     * @param array $photos
     * @param int $price
     * @param string $description
     * @param int $quantity
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @param int $category_id
     */
    
    
    public function __construct( //paramètres du constructeur optionnel
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

    //les getters

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPhotos(): array {
        return $this->photos;
    }

    public function getPrice(): int {
        return $this->price;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function getCategoryId(): int {
        return $this->category_id;
    }


    // les setters


    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setPhotos(array $photos): void {
        $this->photos = $photos;
    }

    public function setPrice(int $price): void {
        if ($price >= 0) { 
            $this->price = $price;
        }
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setQuantity(int $quantity): void {
        if ($quantity >= 0) { 
            $this->quantity = $quantity;
        }
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    
    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }


    public function setCategoryId(int $category_id): void {
        $this->category_id = $category_id;
    }

    /**
     * NOUVEAU : Setter statique pour injecter la connexion PDO.
     * @param PDO $pdo L'objet de connexion à la base de données.
     */
    public static function setPdo(PDO $pdo): void {
        self::$pdo = $pdo;
    }
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