<?php

namespace Lidmo\WP\Foundation\Contracts;

interface Middleware
{
    public function handle($request, $next);
}