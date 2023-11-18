<?php

namespace VicidialApi\Responses\Concerns;

use VicidialApi\Responses\Meta\MetaInformation;

trait HasMetaInformation
{
    public function meta(): MetaInformation
    {
        return $this->meta;
    }
}
