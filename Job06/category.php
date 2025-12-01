<?php

class Category {
   
    private static ?PDO $pdo = null;
    private ?int $id;
    private ?string $name;
    private ?string $description;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    /**
     * Constructeur de la classe Category.
     */
    public function __construct(
        ?int $id = null,
        ?string $name = null,
        ?string $description = null,
        DateTime $createdAt = new DateTime(),
        DateTime $updatedAt = new DateTime()
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
    public static function setPdo(PDO $pdo): void {
        self::$pdo = $pdo;
    }

    // les getters

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    // les setters

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
    public function getProducts(): array {
        if (self::$pdo === null) {
            throw new Exception("La connexion PDO n'a pas été initialisée pour la classe Category.");
        }
        if ($this->id === null) {
            return []; // Retourne un tableau vide si la catégorie n'a pas d'ID
        }

        $products = [];
        
        $sql = "SELECT * FROM product WHERE category_id = :category_id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['category_id' => $this->id]);
        
        
        while ($product_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            // hydratation
            
            
            $product = new Product(); 
            
            
            $product->setId((int) $product_row['id']);
            $product->setName($product_row['name']);
            $product->setPhotos(explode(',', $product_row['photos']));
            $product->setPrice((int) $product_row['price']);
            $product->setDescription($product_row['description']);
            $product->setQuantity((int) $product_row['quantity']);
            $product->setCreatedAt(new DateTime($product_row['createdAt']));
            $product->setUpdatedAt(new DateTime($product_row['updatedAt']));
            $product->setCategoryId((int) $product_row['category_id']);

            
            
            $products[] = $product;
        }

        return $products;
    }
}
?>