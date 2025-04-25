<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';

// Récupération de l'ID du restaurant
$restaurant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Récupération des informations du restaurant
$stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
    header('Location: restaurants.php');
    exit;
}

// Récupération des produits du restaurant
$stmt = $conn->prepare("SELECT * FROM products WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    
    // Vérification que le produit appartient bien au restaurant
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$product_id, $restaurant_id]);
    if ($stmt->fetch()) {
        // Création de la commande
        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        
        // Redirection vers la page de suivi
        header('Location: track_order.php?id=' . $conn->lastInsertId());
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #ff8000;
            --secondary-color: #ff6b2b;
            --accent-color: #ffa64d;
            --text-color: #333;
            --light-bg: #fff5eb;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .restaurant-header {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('img/restaurants/<?php echo htmlspecialchars($restaurant['image']); ?>');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
            border-radius: 15px;
        }

        .restaurant-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .restaurant-address {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .product-image-container {
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .product-image-container i {
            font-size: 3rem;
            color: #dee2e6;
        }

        .product-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            background-color: white;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .product-body {
            padding: 1.5rem;
        }

        .product-title {
            color: var(--text-color);
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .product-price {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .btn-order {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-order:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .no-image-placeholder {
            background: linear-gradient(135deg, var(--light-bg), #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
        }

        .no-image-placeholder i {
            font-size: 3rem;
            color: var(--primary-color);
            opacity: 0.5;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 1rem 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-color);
        }

        @media (max-width: 768px) {
            .restaurant-header {
                padding: 2rem 0;
            }

            .restaurant-title {
                font-size: 2rem;
            }

            .product-card {
                margin-bottom: 1.5rem;
            }
        }

        .product-icon {
            font-size: 80px;
            background: white;
            width: 120px;
            height: 120px;
            border-radius: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="restaurants.php">
                <i class="bi bi-basket-fill me-2"></i>
                Food Delivery
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="restaurants.php">Restaurants</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($restaurant['name']); ?></li>
            </ol>
        </nav>

        <div class="restaurant-header text-center">
            <h1 class="restaurant-title"><?php echo htmlspecialchars($restaurant['name']); ?></h1>
            <p class="restaurant-address">
                <i class="bi bi-geo-alt-fill me-2"></i>
                <?php echo htmlspecialchars($restaurant['address']); ?>
            </p>
        </div>

        <h2 class="mb-4">Menu</h2>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card h-100">
                        <?php 
                        $image_path = "img/products/" . $product['image'];
                        if (file_exists($image_path)): 
                        ?>
                            <img src="<?php echo $image_path; ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.onerror=null; this.src='img/products/default.jpg';">
                        <?php else: ?>
                            <div class="product-image-container">
                                <i class="bi bi-image"></i>
                                <div class="text-muted small mt-2">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="product-body">
                            <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="product-description">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </p>
                            <p class="product-price">
                                <?php echo number_format($product['price'], 2); ?> €
                            </p>
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-order">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Commander
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 