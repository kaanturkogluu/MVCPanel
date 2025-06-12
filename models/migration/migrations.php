<?php
require_once __DIR__ . '/../../config/config.php';
require_once 'Blueprint.php';
require_once __DIR__ . '/../../classes/database.php';

// Database bağlantısını oluştur
$database = Database::getInstance('default');
$db = $database->getConnection();


// ---------- ***** Admin Paneli Users Tablosu ***** ----------
$userBlueprint = new Blueprint('ac_users');
$userBlueprint->id()
    ->string('username')
    ->unique()
    ->string('password')
    ->string('user_token')
    ->timestamp('last_login')
    ->timestamps();
// ---------- ***** Admin Paneli Giriş - Çıkış Kaydı  , İşlemler ile genişletilebilir***** ----------
$logBluePrint = new Blueprint('ac_login_logs');
$logBluePrint->id()
    ->string('user_name')
    ->enum('proccess', ['login', 'logout'])
    ->string('ip')
    ->timestamp('proccess_time')->default('CURRENT_TIMESTAMP');


// Migration'ları çalıştır
$migrations = [
    $userBlueprint->create() => 'Admin Tablosu',
    $logBluePrint->create() => 'Log Tablosu'
];


// Admin kullanıcısı oluştur

try {
    $say = 0;
    foreach ($migrations as $sql => $table) {
        $say++;
        echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<strong>Tablo İşlemi Başlatılıyor:</strong> " . strtoupper($table) . "<br>";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            echo "<span style='color: green;'>✓ Tablo başarıyla oluşturuldu: " . $table . "</span><br>";
            echo "<small>SQL Sorgusu:</small> <code>" . htmlspecialchars($sql) . "</code>";
        } catch (PDOException $e) {
            echo "<span style='color: red;'>✗ Hata: " . $e->getMessage() . "</span><br>";
            echo "<small>Hatalı SQL Sorgusu:</small> <code>" . htmlspecialchars($sql) . "</code>";
        }

        echo "</div>";
        echo "<br>";
    }


    echo $say . " Tane Tablo Oluşturuldu . <br>";
    echo "Tüm migration'lar başarıyla tamamlandı.\n";
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}