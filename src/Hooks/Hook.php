<?php

namespace Lidmo\WP\Foundation\Hooks;

use Illuminate\Support\Str;
use Lidmo\WP\Foundation\Contracts\Hook as BaseHook;

abstract class Hook implements BaseHook
{
    protected $name;

    protected $type;
    protected $priority = 100;

    protected $acceptedArgs = 1;

    public function __construct()
    {
        $class = explode('\\', get_class($this));
        $this->name = Str::snake(array_pop($class));
        $this->type = str_singular(strtolower(array_pop($class)));
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