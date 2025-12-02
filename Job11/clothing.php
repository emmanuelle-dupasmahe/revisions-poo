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