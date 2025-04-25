-- Ajout de la colonne image à la table products
ALTER TABLE products ADD COLUMN image VARCHAR(255) DEFAULT 'default.png' AFTER price;

-- Mise à jour des images pour les produits existants
UPDATE products SET image = 'coq_au_vin.png' WHERE name = 'Coq au Vin';
UPDATE products SET image = 'boeuf_bourguignon.png' WHERE name = 'Boeuf Bourguignon';
UPDATE products SET image = 'sushi_deluxe.png' WHERE name = 'Plateau Sushi Deluxe';
UPDATE products SET image = 'california_rolls.png' WHERE name = 'California Rolls';
UPDATE products SET image = 'pizza_margherita.png' WHERE name = 'Pizza Margherita';
UPDATE products SET image = 'pizza_4_fromages.png' WHERE name = 'Pizza 4 Fromages';
UPDATE products SET image = 'couscous_royal.png' WHERE name = 'Couscous Royal';
UPDATE products SET image = 'tajine_poulet.png' WHERE name = 'Tajine Poulet';
UPDATE products SET image = 'classic_burger.png' WHERE name = 'Classic Burger';
UPDATE products SET image = 'veggie_burger.png' WHERE name = 'Veggie Burger';
UPDATE products SET image = 'canard_laque.png' WHERE name = 'Canard Laqué';
UPDATE products SET image = 'nouilles_sautees.png' WHERE name = 'Nouilles Sautées'; 