<?php
// Fonction pour générer une image PNG
function generateImage($name, $color, $icon) {
    $width = 400;
    $height = 300;
    
    // Créer une nouvelle image
    $image = imagecreatetruecolor($width, $height);
    
    // Activer la transparence
    imagealphablending($image, true);
    imagesavealpha($image, true);
    
    // Convertir les couleurs hex en RGB
    $color = sscanf($color, "#%02x%02x%02x");
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $mainColor = imagecolorallocate($image, $color[0], $color[1], $color[2]);
    $textColor = imagecolorallocate($image, 255, 255, 255);
    
    // Remplir le fond
    imagefill($image, 0, 0, $backgroundColor);
    
    // Dessiner le rectangle principal
    imagefilledrectangle($image, 50, 50, 350, 250, $mainColor);
    
    // Ajouter le texte
    $font = 'C:\Windows\Fonts\arial.ttf'; // Chemin vers la police Arial
    imagettftext($image, 20, 0, 200 - (strlen($name) * 5), 200, $textColor, $font, $name);
    
    // Ajouter l'icône (texte plus grand)
    imagettftext($image, 40, 0, 180, 150, $textColor, $font, $icon);
    
    // Capturer la sortie
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    
    // Libérer la mémoire
    imagedestroy($image);
    
    return $imageData;
}

// Liste des produits avec leurs couleurs et icônes
$products = [
    'coq_au_vin' => ['Coq au Vin', '#8B4513', '🍗'],
    'boeuf_bourguignon' => ['Boeuf Bourguignon', '#A0522D', '🥘'],
    'sushi_deluxe' => ['Sushi Deluxe', '#4682B4', '🍱'],
    'california_rolls' => ['California Rolls', '#32CD32', '🍣'],
    'pizza_margherita' => ['Pizza Margherita', '#FF6347', '🍕'],
    'pizza_4_fromages' => ['Pizza 4 Fromages', '#FFD700', '🧀'],
    'couscous_royal' => ['Couscous Royal', '#DAA520', '🍲'],
    'tajine_poulet' => ['Tajine Poulet', '#CD853F', '🍖'],
    'classic_burger' => ['Classic Burger', '#8B0000', '🍔'],
    'veggie_burger' => ['Veggie Burger', '#228B22', '🥬'],
    'canard_laque' => ['Canard Laqué', '#B8860B', '🦆'],
    'nouilles_sautees' => ['Nouilles Sautées', '#FFA500', '🍜']
];

// Créer le dossier s'il n'existe pas
if (!file_exists('img/products')) {
    mkdir('img/products', 0777, true);
}

// Supprimer les anciens fichiers
array_map('unlink', glob('img/products/*.*'));

// Générer les images
foreach ($products as $filename => $data) {
    $imageData = generateImage($data[0], $data[1], $data[2]);
    file_put_contents("img/products/{$filename}.png", $imageData);
    echo "Image générée : {$filename}.png\n";
}

// Créer l'image par défaut
$defaultImageData = generateImage('Produit', '#6c757d', '🍽️');
file_put_contents('img/products/default.png', $defaultImageData);
echo "Image par défaut générée : default.png\n";

echo "Toutes les images ont été générées avec succès !";
?> 