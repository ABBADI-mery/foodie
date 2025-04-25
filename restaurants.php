<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';

// Récupération de tous les restaurants
$stmt = $conn->query("SELECT * FROM restaurants ORDER BY name");
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants - Glovo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary-color: #ff8000;
            --secondary-color: #ff6b2b;
            --accent-color: #ff8000;
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

        .page-title {
            color: var(--text-color);
            font-weight: bold;
            margin: 2rem 0;
            text-align: center;
            position: relative;
        }

        .page-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
            margin: 10px auto;
        }

        .restaurant-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .restaurant-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: var(--text-color);
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .restaurant-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .address {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 1.5rem;
        }

        .address i {
            color: var(--primary-color);
            margin-right: 5px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
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

        @media (max-width: 768px) {
            .restaurant-card {
                margin-bottom: 1.5rem;
            }
            
            .page-title {
                font-size: 1.8rem;
                margin: 1.5rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Découvrez les meilleurs restaurants</h1>
            <p class="hero-text">Des restaurants soigneusement sélectionnés pour vous offrir une expérience culinaire exceptionnelle</p>
            <a href="#restaurants" class="btn-light">Explorer les restaurants</a>
        </div>
    </section>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
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

    <!-- Restaurants Section -->
    <section id="restaurants" class="container py-5">
        <h1 class="page-title">Nos restaurants partenaires</h1>
        
        <div class="row g-4">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card restaurant-card h-100">
                        <?php 
                        $image_path = "img/restaurants/" . htmlspecialchars($restaurant['image']);
                        if (file_exists($image_path)): 
                        ?>
                            <img src="<?php echo $image_path; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="bi bi-shop"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                            <p class="restaurant-description">
                                <?php echo htmlspecialchars($restaurant['description']); ?>
                            </p>
                            <p class="address">
                                <i class="bi bi-geo-alt-fill"></i>
                                <?php echo htmlspecialchars($restaurant['address']); ?>
                            </p>
                            <a href="restaurant_detail.php?id=<?php echo $restaurant['id']; ?>" 
                               class="btn btn-primary w-100">
                                <i class="bi bi-menu-button me-2"></i>
                                Voir le menu
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 