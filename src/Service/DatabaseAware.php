<?php

namespace Comment\Service;

use MongoDB\Database;

interface DatabaseAware
{
    public function setDriver(Database $client): self;
}
