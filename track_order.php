<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'config/database.php';

// Récupération de l'ID de la commande
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Récupération des informations de la commande
$stmt = $conn->prepare("
    SELECT o.*, p.name as product_name, r.name as restaurant_name, 
           r.latitude as restaurant_lat, r.longitude as restaurant_lng,
           r.address as restaurant_address
    FROM orders o
    JOIN products p ON o.product_id = p.id
    JOIN restaurants r ON p.restaurant_id = r.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: restaurants.php');
    exit;
}

// Simulation de la position du livreur (pour démonstration)
$delivery_progress = rand(0, 100);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de commande - Food Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            color: white !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
        }

        .nav-link:hover {
            color: white !important;
        }

        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .progress {
            height: 25px;
            background-color: var(--light-bg);
        }

        .progress-bar {
            background-color: var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
        }

        h1 {
            color: var(--text-color);
            font-weight: 600;
        }

        .delivery-steps {
            position: relative;
            padding: 20px 0;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            position: relative;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .step.active {
            opacity: 1;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 25px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background-color: var(--primary-color);
            opacity: 0.3;
        }

        .step-icon {
            width: 50px;
            height: 50px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 1.2rem;
        }

        .step.active .step-icon {
            background-color: var(--primary-color);
            box-shadow: 0 0 0 5px rgba(255, 128, 0, 0.2);
        }

        .step-content h6 {
            margin: 0;
            color: var(--text-color);
            font-weight: 600;
        }

        .delivery-time {
            text-align: center;
            padding: 20px;
            background-color: var(--light-bg);
            border-radius: 10px;
        }

        .time-display {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 10px;
        }

        .driver-profile {
            display: flex;
            align-items: center;
        }

        .driver-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }

        .contact-options .btn {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .contact-options .btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="restaurants.php">Food Delivery</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="mb-4">Suivi de votre commande</h1>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Détails de la commande #<?php echo $order_id; ?></h5>
                        <p class="mb-2"><strong>Restaurant :</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                        <p class="mb-2"><strong>Produit :</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
                        <p class="mb-2"><strong>Adresse :</strong> <?php echo htmlspecialchars($order['restaurant_address']); ?></p>
                        
                        <div class="delivery-steps mt-4">
                            <div class="step <?php echo $delivery_progress >= 0 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Commande reçue</h6>
                                    <p class="text-muted small">Votre commande a été reçue par le restaurant</p>
                                </div>
                            </div>
                            <div class="step <?php echo $delivery_progress >= 25 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <i class="bi bi-fire"></i>
                                </div>
                                <div class="step-content">
                                    <h6>En préparation</h6>
                                    <p class="text-muted small">Le restaurant prépare votre commande</p>
                                </div>
                            </div>
                            <div class="step <?php echo $delivery_progress >= 50 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <i class="bi bi-bicycle"></i>
                                </div>
                                <div class="step-content">
                                    <h6>En route</h6>
                                    <p class="text-muted small">Le livreur est en route vers votre adresse</p>
                                </div>
                            </div>
                            <div class="step <?php echo $delivery_progress >= 100 ? 'active' : ''; ?>">
                                <div class="step-icon">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Livré</h6>
                                    <p class="text-muted small">Votre commande a été livrée</p>
                                </div>
                            </div>
                        </div>

                        <div class="delivery-time mt-4">
                            <h6>Temps estimé de livraison</h6>
                            <div class="time-display" id="deliveryTimer">30:00</div>
                        </div>
                    </div>
                </div>

                <div id="map"></div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informations du livreur</h5>
                        <div class="delivery-info">
                            <div class="driver-profile mb-3">
                                <img src="img/driver-avatar.png" alt="Livreur" class="driver-avatar">
                                <div class="driver-details">
                                    <h6>John Doe</h6>
                                    <p class="text-muted small mb-0">⭐ 4.8 (203 livraisons)</p>
                                </div>
                            </div>
                            <div class="contact-options">
                                <button class="btn btn-outline-primary mb-2 w-100">
                                    <i class="bi bi-chat"></i> Message
                                </button>
                                <button class="btn btn-outline-primary w-100">
                                    <i class="bi bi-telephone"></i> Appeler
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map;
        let deliveryMarker;
        let restaurantMarker;
        const restaurantPosition = {
            lat: <?php echo $order['restaurant_lat']; ?>,
            lng: <?php echo $order['restaurant_lng']; ?>
        };
        
        // Position simulée du client (Paris)
        const customerPosition = {
            lat: 48.8566,
            lng: 2.3522
        };

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: restaurantPosition,
                zoom: 13
            });

            // Marqueur du restaurant
            restaurantMarker = new google.maps.Marker({
                position: restaurantPosition,
                map: map,
                title: '<?php echo htmlspecialchars($order['restaurant_name']); ?>',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                }
            });

            // Marqueur du client
            new google.maps.Marker({
                position: customerPosition,
                map: map,
                title: 'Votre position',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                }
            });

            // Marqueur du livreur
            deliveryMarker = new google.maps.Marker({
                position: restaurantPosition,
                map: map,
                title: 'Livreur',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                }
            });

            // Tracé de l'itinéraire
            const directionsService = new google.maps.DirectionsService();
            const directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true
            });

            const request = {
                origin: restaurantPosition,
                destination: customerPosition,
                travelMode: 'DRIVING'
            };

            directionsService.route(request, function(result, status) {
                if (status == 'OK') {
                    directionsRenderer.setDirections(result);
                    animateDelivery(result.routes[0].overview_path);
                }
            });
        }

        function animateDelivery(path) {
            let i = 0;
            const numSteps = 100;
            const delay = 100;

            function animate() {
                if (i < numSteps) {
                    const progress = i / numSteps;
                    const index = Math.floor(progress * (path.length - 1));
                    const position = path[index];
                    
                    deliveryMarker.setPosition(position);
                    i++;
                    setTimeout(animate, delay);
                }
            }

            animate();
        }

        // Minuteur de livraison
        function startDeliveryTimer() {
            let timeLeft = 30 * 60; // 30 minutes en secondes
            const timerDisplay = document.getElementById('deliveryTimer');

            const timer = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                
                timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    timerDisplay.textContent = "Livré !";
                }
                
                timeLeft--;
            }, 1000);
        }

        // Simulation de la progression de la livraison
        function updateDeliveryProgress() {
            let progress = 0;
            const steps = document.querySelectorAll('.step');
            
            const interval = setInterval(() => {
                progress += 25;
                
                steps.forEach((step, index) => {
                    if (index * 25 <= progress) {
                        step.classList.add('active');
                    }
                });
                
                if (progress >= 100) {
                    clearInterval(interval);
                }
            }, 30000); // Change d'étape toutes les 30 secondes
        }

        // Démarrer le suivi
        document.addEventListener('DOMContentLoaded', () => {
            startDeliveryTimer();
            updateDeliveryProgress();
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=VOTRE_CLE_API&callback=initMap" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 