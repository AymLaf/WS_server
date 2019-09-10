<?php


namespace App\Classes;


class Room
{
    private $participants;
    private $id;
    private $eyesonLink;

    public function __construct($id)
    {
        $this->participants = [
            'lead' => null,
            'other' => []
        ];
        $this->id = $id;
        //TODO call API get eyeson link
        $this->eyesonLink = "https://app.eyeson.team/?guest=dDDcS0IwKdyuEiQHioOmWkbR";
    }

    public function addParticipant($id_conn, $participant)
    {
        $this->participants['other'][$id_conn] = $participant;
    }

    /**
     * @return mixed
     */
    public function getEyesonLink()
    {
        return $this->eyesonLink;
    }

    /**
     * @param mixed $eyesonLink
     */
    public function setEyesonLink($eyesonLink): void
    {
        $this->eyesonLink = $eyesonLink;
    }


}