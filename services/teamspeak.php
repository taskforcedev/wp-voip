<?php namespace Voip\Services;

use Voip\lib\TeamSpeak as TeamSpeakService;

class TeamSpeak
{
    private $ts3;
    private $host = "";
    private $query_port = 10011;
    public $server_port = 9987;
    private $token = "";

    /**
     * Connect to the TeamSpeak Server
     * @return TeamSpeakService
     */
    public function connect()
    {
        $this->ts3 = new TeamSpeakService($this->host, $this->query_port);
        $this->ts3->connect();
        return $this->ts3;
    }

    public function disconnect()
    {
        $this->ts3->quit();
        $this->ts3 = '';
    }

    public function client()
    {
        $this->checkServer();
        return $this->ts3;
    }

    public function checkServer()
    {
        if (!is_object($this->ts3)) {
            $this->ts3 = $this->connect();
            $this->ts3->selectServer($this->server_port);
        }
    }

    public function getClients()
    {
        $clients = $this->ts3->clientList();
        $clients = $clients['data'];
        return $clients;
    }

    public function getServerGroups()
    {
        $groups = $this->ts3->serverGroupList();
        $groups = $groups['data'];
        return $groups;
    }

    public function kickUser($guid, $nickname)
    {
        /* Handle Kick Logic Here */
        $this->ts3->tokenUse($this->token);
        $this->ts3->clientKick($guid);

        $eventData = array(
            'authorised_by' => \Auth::user()->username,
            'kicked' => $nickname,
        );

        /* Fire event for notifications */
        \Event::fire('teamspeak.user.kicked', array($eventData));
    }
}

