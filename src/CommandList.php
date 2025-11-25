<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Iterator;
use Sevit\PrikolBot\Commands\InvokableCommandInterface;

final class CommandList implements Iterator
{
    private array $commands = [];

    /**
     * @param string $name
     * @param class-string<InvokableCommandInterface> $commandClass
     * @return $this
     */
    public function addCommand(string $name, string $commandClass): self
    {
//        $class = new $commandClass();
//        if (!$class instanceof AbstractCommand) {
//            throw new InvalidArgumentException('Команда должна наследовать класс AbstractCommand');
//        }

        $this->commands[$name] = $commandClass;
        return $this;
    }

    public function current(): mixed
    {
        return current($this->commands);
    }

    public function next(): void
    {
        next($this->commands);
    }

    public function key(): mixed
    {
        return key($this->commands);
    }

    public function valid(): bool
    {
        return key($this->commands) !== null;
    }

    public function rewind(): void
    {
        reset($this->commands);
    }
}