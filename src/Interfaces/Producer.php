<?php
declare(strict_types=1);

namespace GuidePilot\UndergroundRadio\Interfaces;

interface Producer {

    public function send(Message $message, MessageDestination $destination);


}