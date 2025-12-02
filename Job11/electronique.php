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
        $this->brand = $brand;
        $this->warranty_fee = $warranty_fee;
    }


    // Getters 
    public function getBrand(): ?string { return $this->brand; }
    public function getWarrantyFee(): ?int { return $this->warranty_fee; }

    // Setters 
    public function setBrand(?string $brand): void { $this->brand = $brand; }
    public function setWarrantyFee(?int $warranty_fee): void { $this->warranty_fee = $warranty_fee; }
}

?>