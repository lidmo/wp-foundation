<?php

namespace Lidmo\WP\Plugin\Hooks;

use Lidmo\WP\Plugin\Contracts\Hook as BaseHook;

abstract class Hook implements BaseHook
{
    protected $priority = 100;

    protected $acceptedArgs = 1;

    /**
     * @throws \Exception
     */
    public function getName(): string
    {
        if(isset($this->name)) {
            return $this->name;
        }
        throw new \Exception('Hook name undefined');
    }

    /**
     * @throws \Exception
     */
    public function getType(): string
    {
        if(isset($this->type)) {
            return $this->type;
        }
        throw new \Exception('Hook type undefined');
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