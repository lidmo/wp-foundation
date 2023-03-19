<?php

namespace Lidmo\WP\Foundation\Hooks;

use Lidmo\WP\Foundation\Contracts\Hook as BaseHook;
use Lidmo\WP\Foundation\Support\Str;

abstract class Hook implements BaseHook
{
    private $name;

    private $type;
    protected $priority = 100;

    protected $acceptedArgs = 1;

    public function __construct()
    {
        $className = Str::snake(Str::replace('\\', '', Str::after(static::class, '\\Hooks\\')));
        $this->name = $this->name ?? Str::beforeLast($className, '_');
        $this->type = $this->type ?? Str::afterLast($className, '_');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getAcceptedArgs(): int
    {
        return $this->acceptedArgs;
    }

}