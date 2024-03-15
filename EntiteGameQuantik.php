<?php

class EntiteGameQuantik
{
    public int $gameid;
    public int $playerone;
    public ?int $playertwo = null;
    public string $gamestatus; // was = 'init';

    public ?string $json = '';
    public function getGameId(): ?int
    {
        return $this->gameid;
    }
    public function setGameId(int $gameId): void
    {
        $this->gameid = $gameId;
    }

    public function __toString(): string
    {
        return $this->playerone.' '.$this->gameid;
    }

}