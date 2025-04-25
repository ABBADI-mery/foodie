<?php
session_start();

// Redirection si déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: restaurants.php');
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';
    
    if(isset($_POST['action'])) {
        if($_POST['action'] === 'login') {
            // Traitement de la connexion
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: restaurants.php');
                exit;
            } else {
                $error = "Identifiants incorrects";
            }
        } elseif($_POST['action'] === 'register') {
            // Traitement de l'inscription
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirmation = $_POST['password_confirmation'] ?? '';
            
            // Validation
            $errors = [];
            if(empty($name)) $errors[] = "Le nom est requis";
            if(empty($email)) $errors[] = "L'email est requis";
            if(empty($password)) $errors[] = "Le mot de passe est requis";
            if($password !== $password_confirmation) $errors[] = "Les mots de passe ne correspondent pas";
            
            if(empty($errors)) {
                // Vérifier si l'email existe déjà
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if($stmt->fetch()) {
                    $errors[] = "Cet email est déjà utilisé";
                } else {
                    // Créer le nouvel utilisateur
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    if($stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)])) {
                        $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                        header('Location: index.php');
                        exit;
                    } else {
                        $errors[] = "Une erreur est survenue lors de l'inscription";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification - Food Delivery</title>
    <link rel="stylesheet" href="assets/css/css/normalize.css">
    <link rel="stylesheet" href="assets/css/css/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff8000;
            --secondary-color: #ff6b2b;
            --accent-color: #ffa64d;
            --text-color: #333;
            --light-bg: #fff5eb;
            --error-color: #dc3545;
            --success-color: #28a745;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 1000px;
        }

        .auth-row {
            display: flex;
            flex-wrap: wrap;
        }

        .auth-side {
            flex: 1;
            padding: 3rem;
        }

        .auth-side.left {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .auth-side.right {
            background: white;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }

        .auth-side.left .auth-title {
            color: white;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 128, 0, 0.25);
        }

        .btn-auth {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-auth:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 128, 0, 0.3);
        }

        .auth-switch {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-switch a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-switch a:hover {
            color: var(--secondary-color);
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        @media (max-width: 768px) {
            .auth-side {
                flex: 100%;
                padding: 2rem;
            }
            
            .auth-side.left {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- Formulaire de connexion -->
                <form method="POST" action="" class="sign-in-form">
                    <input type="hidden" name="action" value="login">
                    <h2 class="title">Connexion</h2>
                    
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Mot de passe" required>
                    </div>

                    <input type="submit" value="Se connecter" class="btn solid">
                </form>

                <!-- Formulaire d'inscription -->
                <form method="POST" action="" class="sign-up-form">
                    <input type="hidden" name="action" value="register">
                    <h2 class="title">Inscription</h2>
                    
                    <?php if(isset($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="name" placeholder="Nom d'utilisateur" required>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Mot de passe" required>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                    </div>

                    <input type="submit" value="S'inscrire" class="btn solid">
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>Nouveau ici ?</h3>
                    <p>Rejoignez-nous pour découvrir les meilleurs restaurants de votre région !</p>
                    <button class="btn transparent" id="sign-up-btn">S'inscrire</button>
                </div>
                <img src="img/undraw_food_delivery.svg" class="image" alt="">
            </div>

            <div class="panel right-panel">
                <div class="content">
                    <h3>Déjà membre ?</h3>
                    <p>Connectez-vous pour commander vos plats préférés !</p>
                    <button class="btn transparent" id="sign-in-btn">Se connecter</button>
                </div>
                <img src="img/undraw_welcome.svg" class="image" alt="">
            </div>
        </div>
    </div>

    <script src="assets/js/js/main.js"></script>
</body>
</html> 