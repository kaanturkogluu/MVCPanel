<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/../vendor/autoload.php';


class ChatServer implements MessageComponentInterface
{
    protected $clients;  // tüm bağlantılar
    protected $rooms;    // oda bilgisi, client-id map vs.
    protected $pendingChats; // onay bekleyen sohbetler

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
        $this->pendingChats = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        parse_str($conn->httpRequest->getUri()->getQuery(), $query);
        $userType = $query['user'] ?? 'guest';
        $userId = $query['id'] ?? null;
        $room = $query['room'] ?? null;
    
        $conn->userType = $userType;
        $conn->userId = $userId;
        $conn->room = $room;
        $conn->isApproved = false;
    
        $this->clients->attach($conn);
    
        if (!isset($this->rooms[$room])) {
            $this->rooms[$room] = new \SplObjectStorage;
        }
        $this->rooms[$room]->attach($conn);
    
        echo "Bağlandı: {$userType} ID: {$userId} Oda: {$room}\n";
    
        // Admin'e bağlı client listesini gönder
        if ($userType === 'admin') {
            $activeClients = [];
        
            foreach ($this->clients as $clientConn) {
                if ($clientConn !== $conn && $clientConn->userType === 'client') {
                    $activeClients[] = [
                        'userId' => $clientConn->userId,
                        'room' => $clientConn->room
                    ];
                }
            }
        
            $conn->send(json_encode([
                'type' => 'client_list',
                'clients' => $activeClients
            ]));
        }
    }
    

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        $room = $from->room;

        if (!$room) return;

        // İlk mesaj kontrolü
        if (isset($data['type']) && $data['type'] === 'initial_message') {
            // Müşteri mesajını kaydet ve adminlere bildir
            $this->pendingChats[$from->userId] = [
                'client' => $from,
                'message' => $data['content'],
                'timestamp' => $data['timestamp'],
                'room' => $room
            ];

            // Adminlere bildirim gönder
            foreach ($this->rooms[$room] as $client) {
                if ($client->userType === 'admin') {
                    $notification = json_encode([
                        'type' => 'new_chat_request',
                        'userId' => $from->userId,
                        'message' => $data['content'],
                        'timestamp' => $data['timestamp']
                    ]);
                    $client->send($notification);
                }
            }
            return;
        }

        // Admin onay mesajı kontrolü
        if (isset($data['type']) && $data['type'] === 'approve_chat') {
            $userId = $data['userId'];
            if (isset($this->pendingChats[$userId])) {
                $client = $this->pendingChats[$userId]['client'];
                $client->isApproved = true;

                // Müşteriye onay mesajı gönder
                $approvalMessage = json_encode([
                    'type' => 'admin_approval',
                    'message' => 'Chat onaylandı'
                ]);
                $client->send($approvalMessage);

                // Admin'e onay bilgisi gönder
                $adminNotification = json_encode([
                    'type' => 'chat_approved',
                    'userId' => $userId
                ]);
                $from->send($adminNotification);

                // Bekleyen sohbetlerden kaldır
                unset($this->pendingChats[$userId]);
            }
            return;
        }

        // Normal mesaj işleme (sadece onaylı sohbetler için)
        if ($from->isApproved) {
            foreach ($this->rooms[$room] as $client) {
                if ($client !== $from && $client->isApproved) {
                    $client->send($msg);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Bekleyen sohbetlerden kaldır
        if (isset($this->pendingChats[$conn->userId])) {
            unset($this->pendingChats[$conn->userId]);
        }

        $this->clients->detach($conn);
        if (isset($this->rooms[$conn->room])) {
            $this->rooms[$conn->room]->detach($conn);
        }
        echo "Bağlantı kapandı\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Hata: {$e->getMessage()}\n";
        $conn->close();
    }
}


require_once __DIR__ . "/../config/config.php";

// WebSocket sunucusu için port numarasını global config'den al
$port = $GLOBALS['app_config']['websocket_port'] ?? 8080; // varsayılan port 8080

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new ChatServer()
        )
    ),
    $port
);
$server->run();
