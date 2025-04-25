-- Création de la base de données
CREATE DATABASE IF NOT EXISTS food_delivery;
USE food_delivery;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des restaurants
CREATE TABLE IF NOT EXISTS restaurants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT 'default.jpg',
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    restaurant_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    status ENUM('pending', 'in_delivery', 'delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insertion de données de test
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password
('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertion des restaurants
INSERT INTO restaurants (name, address, description, image, latitude, longitude) VALUES
('Le Petit Bistrot', '15 Rue de la Gastronomie, Paris', 'Une cuisine française authentique dans un cadre chaleureux', 'restaurant1.jpg', 48.8566, 2.3522),
('Sushi Master', '8 Avenue des Sushis, Lyon', 'Les meilleurs sushis de la ville préparés par nos chefs japonais', 'restaurant2.jpg', 45.7640, 4.8357),
('La Pizza Della Mamma', '25 Rue de Naples, Marseille', 'Pizzas authentiques cuites au feu de bois', 'restaurant3.jpg', 43.2965, 5.3698),
('Le Couscous Royal', '12 Rue du Maghreb, Toulouse', 'Spécialités orientales et couscous fait maison', 'restaurant4.jpg', 43.6047, 1.4442),
('Burger House', '45 Avenue des Fast-Food, Nice', 'Les meilleurs burgers artisanaux de la région', 'restaurant5.jpg', 43.7102, 7.2620),
('Le Dragon d\'Or', '18 Rue de Chine, Bordeaux', 'Cuisine asiatique raffinée et moderne', 'restaurant6.jpg', 44.8378, 0.5792);

-- Insertion des produits pour chaque restaurant
INSERT INTO products (restaurant_id, name, description, price, image) VALUES
(1, 'Coq au Vin', 'Plat traditionnel français avec du vin rouge', 22.99, 'coq_au_vin.jpg'),
(1, 'Boeuf Bourguignon', 'Mijoté de boeuf aux légumes', 24.99, 'boeuf_bourguignon.jpg'),
(2, 'Plateau Sushi Deluxe', '24 pièces variées', 32.99, 'sushi_deluxe.jpg'),
(2, 'California Rolls', '8 pièces avocat-crabe', 14.99, 'california_rolls.jpg'),
(3, 'Pizza Margherita', 'Tomate, mozzarella, basilic', 13.99, 'pizza_margherita.jpg'),
(3, 'Pizza 4 Fromages', 'Mélange de fromages italiens', 15.99, 'pizza_4_fromages.jpg'),
(4, 'Couscous Royal', 'Avec merguez, poulet et agneau', 25.99, 'couscous_royal.jpg'),
(4, 'Tajine Poulet', 'Aux olives et citrons confits', 19.99, 'tajine_poulet.jpg'),
(5, 'Classic Burger', 'Boeuf, cheddar, bacon', 14.99, 'classic_burger.jpg'),
(5, 'Veggie Burger', 'Steak végétal et légumes grillés', 13.99, 'veggie_burger.jpg'),
(6, 'Canard Laqué', 'Spécialité chinoise traditionnelle', 26.99, 'canard_laque.jpg'),
(6, 'Nouilles Sautées', 'Aux légumes et fruits de mer', 16.99, 'nouilles_sautees.jpg');

-- Mise à jour des images pour les produits existants
UPDATE products SET image = 'classic_burger.jpg' WHERE name = 'Classic Burger';
UPDATE products SET image = 'veggie_burger.jpg' WHERE name = 'Veggie Burger';
UPDATE products SET image = 'canard_laque.jpg' WHERE name = 'Canard Laqué';
UPDATE products SET image = 'tajine_poulet.jpg' WHERE name = 'Tajine Poulet'; 