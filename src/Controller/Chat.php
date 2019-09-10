<?php
namespace App\Controller;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Classes\Room as Meeting;

class Chat implements MessageComponentInterface
{
    private $clients;
    private $users;
    private $lRooms;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->users = [];
        $this->lRooms = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
        // Call de la callback clientside
        $conn->send(json_encode(array(
            'action' => 'sendHello'
        )));
        echo sprintf('Connection #%d !'."\n", $conn->resourceId);
    }

    public function onClose(ConnectionInterface $closedConnection)
    {
        $this->clients->detach($closedConnection);
        echo sprintf('Connection #%d has disconnected'."\n", $closedConnection->resourceId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send('An error has occurred: '.$e->getMessage());
        $conn->close();
    }

    /**
     * [onMessage description]
     * @method onMessage
     * @param  ConnectionInterface $conn [description]
     * @param  [JSON.stringify]              $msg  [description]
     * @example subscribe                conn.send(JSON.stringify({command: "subscribe", channel: "global"}));
     * @example groupchat                conn.send(JSON.stringify({command: "groupchat", message: "hello glob", channel: "global"}));
     * @example message                  conn.send(JSON.stringify({command: "message", to: "1", from: "9", message: "it needs xss protection"}));
     * @example register                 conn.send(JSON.stringify({command: "register", userId: 9}));
     */
    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $data = json_decode($msg, true);
        echo sprintf('Message1 : %s'."\n", $data['command']);

        if (isset($data['command']) && !empty($data['command'])
            && isset($data['meeting_id']) && !empty($data['meeting_id'])
            && $this->clients->offsetExists($conn)) {
            echo sprintf('Message2 : %s'."\n", $data['command']);
            switch ($data['command']) {
                case "join":
                     if (isset($this->lRooms[$data['meeting_id']])) {
                         $r = $this->lRooms[$data['meeting_id']];
                         $r->addParticipant($conn->resourceId, $this->clients->offsetGet($conn));
                     }
                    break;
                case "create":
                    echo sprintf('Create'."\n");
                    $this->lRooms[$data['meeting_id']] = new Meeting($data['meeting_id']);
                    $conn->send(json_encode(array(
                        'action' => 'answerCommit',
                        'type' => 'eyeson',
                        'value' => $this->lRooms[$data['meeting_id']]->getEyesonLink()
                    )));
                    break;
                case "action":

                    break;
                default:

                    break;
            }
        }
    }
}