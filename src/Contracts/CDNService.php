<?php

namespace A17\CDN\Contracts;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

interface CDNService
{
    public function invalidate(): void;

    public function invalidateAll(): void;
}
