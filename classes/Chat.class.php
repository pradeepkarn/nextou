<?php
require_once("config.php");
import("includes/class-autoload.inc.php");

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        $this->sendClientList();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (isset($data['to'])) {
            $to = $data['to'];

            foreach ($this->clients as $client) {
                if ($client !== $from && $client->resourceId == $to) {
                    $client->send(json_encode([
                        'from' => $from->resourceId,
                        'message' => $data['message'],
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        $this->sendClientList();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
        $this->sendClientList();
    }

    protected function sendClientList()
    {
        $clientList = [];

        foreach ($this->clients as $client) {
            $clientList[] = [
                'id' => $client->resourceId,
            ];
        }

        $clientListMessage = json_encode(['clients' => $clientList]);

        foreach ($this->clients as $client) {
            $client->send($clientListMessage);
        }
    }
}
