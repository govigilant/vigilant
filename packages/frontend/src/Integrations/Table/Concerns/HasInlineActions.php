<?php

namespace Vigilant\Frontend\Integrations\Table\Concerns;

trait HasInlineActions
{
    public function runInlineAction(string $code, mixed $id): void
    {
        $this->runAction($code, [$id]);
    }
}
