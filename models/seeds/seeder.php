<?php 

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Admin.php';
require_once __DIR__ . '/../../classes/Logs.php';

class Seeder
{
    private $db;
    private $admin;
    private $logs;

    public function __construct()
    {
        $database = Database::getInstance('default');
        $this->db = $database->getConnection();
        $this->admin = new Admin();
        $this->logs = new Logs();
    }

    /**
     * Tüm seeder'ları çalıştır
     */
    public function run()
    {
        echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto;'>";
        echo "<h2 style='color: #333;'>Seeder İşlemleri Başlatılıyor</h2>";
        
        try {
            $this->seedAdminUsers();
           
            
            echo "<div style='color: green; margin-top: 20px; padding: 10px; background: #e8f5e9; border-radius: 4px;'>";
            echo "✓ Tüm seeder'lar başarıyla tamamlandı.";
            echo "</div>";
        } catch (Exception $e) {
            echo "<div style='color: red; margin-top: 20px; padding: 10px; background: #ffebee; border-radius: 4px;'>";
            echo "✗ Hata: " . $e->getMessage();
            echo "</div>";
        }
        
        echo "</div>";
    }

    /**
     * Admin kullanıcılarını oluştur
     */
    private function seedAdminUsers()
    {
        echo "<div style='margin: 15px 0; padding: 15px; background: #f5f5f5; border-radius: 4px;'>";
        echo "<h3 style='color: #666; margin-top: 0;'>Admin Kullanıcıları Oluşturuluyor</h3>";

        $adminUsers = [
            [
                'username' => 'admin',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'user_token' => bin2hex(random_bytes(32))
            ],
          
        ];

        foreach ($adminUsers as $user) {
            try {
                // Kullanıcı zaten var mı kontrol et
                $existingUser = $this->admin->findAll(['username' => $user['username']], null, 1);
                
                if (empty($existingUser)) {
                    $this->admin->create($user);
                    echo "<div style='color: green; margin: 5px 0;'>";
                    echo "✓ Kullanıcı oluşturuldu: {$user['username']}";
                    echo "</div>";
                } else {
                    echo "<div style='color: orange; margin: 5px 0;'>";
                    echo "⚠ Kullanıcı zaten mevcut: {$user['username']}";
                    echo "</div>";
                }
            } catch (Exception $e) {
                echo "<div style='color: red; margin: 5px 0;'>";
                echo "✗ Hata ({$user['username']}): " . $e->getMessage();
                echo "</div>";
            }
        }

        echo "</div>";
    }

    /**
     * Örnek login logları oluştur
     */
    
}

// Seeder'ı çalıştır
$seeder = new Seeder();
$seeder->run();





?>