<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface
{
    protected $clients;
    protected $customers = [];
    protected $admins = [];
    protected $rooms = []; // room_id => ['customer' => conn, 'admin' => conn]

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo "WebSocket sunucusu başlatıldı...\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Yeni bağlantı ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!isset($data['type'])) return;

        switch ($data['type']) {
            case 'init':
                // Kullanıcının rolünü tanı
                if ($data['role'] === 'customer') {
                    $from->role = 'customer';
                    $from->name = $data['name'];
                    $this->customers[$from->resourceId] = $from;
                    $this->assignAdmin($from);
                } elseif ($data['role'] === 'admin') {
                    $from->role = 'admin';
                    $from->name = $data['name'];
                    $this->admins[$from->resourceId] = $from;
                }
                break;

            case 'message':
                if (!isset($data['room_id']) || !isset($this->rooms[$data['room_id']])) return;

                $room = $this->rooms[$data['room_id']];
                $toSend = json_encode([
                    'type' => 'message',
                    'from' => $data['from'],
                    'message' => $data['message'],
                    'room_id' => $data['room_id']
                ]);

                // Sadece oda üyelerine gönder
                foreach ($room as $user) {
                    $user->send($toSend);
                }
                break;
        }
    }

    private function assignAdmin(ConnectionInterface $customer)
    {
        // İlk uygun admin'e müşteri ata
        foreach ($this->admins as $admin) {
            $room_id = uniqid('room_');
            $this->rooms[$room_id] = [
                'customer' => $customer,
                'admin' => $admin
            ];

            // Müşteriye gönder
            $customer->send(json_encode([
                'type' => 'matched',
                'room_id' => $room_id,
                'partner' => $admin->name
            ]));

            // Admin'e gönder
            $admin->send(json_encode([
                'type' => 'matched',
                'room_id' => $room_id,
                'partner' => $customer->name
            ]));

            return;
        }

        // Admin yoksa bilgi ver
        $customer->send(json_encode([
            'type' => 'info',
            'message' => 'Şu anda müsait temsilci yok.'
        ]));
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        // Temizleme
        unset($this->customers[$conn->resourceId]);
        unset($this->admins[$conn->resourceId]);

        // Oda içinden çıkarsa temizle
        foreach ($this->rooms as $room_id => $room) {
            if (in_array($conn, $room)) {
                unset($this->rooms[$room_id]);
            }
        }

        echo "Bağlantı kapandı ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Hata: {$e->getMessage()}\n";
        $conn->close();
    }
}

use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    8080
);

$server->run();
