<?php

/**
 * Interface pour tout objet qui doit pouvoir gérer ses quantités en stock.
 */
interface StockableInterface {
    
    /**
     * Ajoute une quantité spécifiée au stock actuel.
     * @param int $stock Quantité à ajouter.
     * @return self Retourne l'instance actuelle pour permettre le chaînage des méthodes.
     */
    public function addStocks(int $stock): self;

    /**
     * Retire une quantité spécifiée du stock actuel.
     * @param int $stock Quantité à retirer.
     * @return self Retourne l'instance actuelle pour permettre le chaînage des méthodes.
     */
    public function removeStocks(int $stock): self;
}