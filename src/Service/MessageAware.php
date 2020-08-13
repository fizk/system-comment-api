<?php

namespace Comment\Service;

interface MessageAware
{
    public function setMessageService(Message $service): self;
}
