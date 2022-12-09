<?php

namespace Odan\Tsid;

interface TsidFactoryInterface
{
    public function generate(): Tsid;
}
