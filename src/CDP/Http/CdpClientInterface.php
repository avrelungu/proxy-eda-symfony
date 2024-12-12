<?php

namespace App\CDP\Http;

use App\CDP\Analytics\Model\ModelInterface;

interface CdpClientInterface
{
    public function track(ModelInterface $modelInterface);

    public function identify(ModelInterface $modelInterface);
}