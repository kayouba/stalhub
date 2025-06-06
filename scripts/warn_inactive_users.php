<?php

use PHPMailer\PHPMailer\PHPMailer;
use App\Lib\Database;

require_once __DIR__ . '/../vendor/autoload.php';

// Connexion sécurisée via .env
$pdo = Database::getConnection();

// Seuil de 21 mois d'inactivité
$seuil = date('Y-m-d H:i:s', strtotime('-21 months'));

$stmt = $pdo->prepare("SELECT * FROM users WHERE last_login_at < ? AND is_active = 1");
$stmt->execute([$seuil]);

$users = $stmt->fetchAll();

foreach ($users as $user) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 1025;
    $mail->SMTPAuth = false;
    $mail->SMTPSecure = false;
    $mail->setFrom('no-reply@stalhub.local', 'StalHub');
    $mail->addAddress($user['email']);
    $mail->Subject = 'Inactivité prolongée sur StalHub';
    $mail->Body = "Bonjour " . htmlspecialchars($user['first_name']) . ",\n\nVous n'avez pas utilisé StalHub depuis plus de 21 mois. 
Si aucune activité n’est enregistrée dans le mois à venir, votre compte sera supprimé automatiquement pour respecter notre politique de confidentialité.";
    $mail->send();
}

echo "Mails de prévention envoyés.";
