<?php

namespace Comment\Service;

interface ResourceAware
{
    public function setResourceService(Resource $service): self;
}
