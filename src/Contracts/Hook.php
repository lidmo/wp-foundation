<?php

namespace Lidmo\WP\Foundation\Contracts;

interface Hook
{
    public function getName(): string;
    public function getType(): string;

    public function getPriority(): int;

    public function getAcceptedArgs(): int;
}