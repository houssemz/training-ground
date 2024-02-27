<?php

declare(strict_types=1);

namespace spec\App\Shared\Matchers;

/** Use this trait to register automatically matchers via trait composition. */
trait RegistrableMatchers
{
    /** @var array<string, callable> */
    private array|null $matchers = null;

    /** @return array<string, callable> */
    public function getMatchers(): array
    {
        if ($this->matchers === null) {
            $this->matchers = [];

            foreach (get_class_methods($this) as $method) {
                if (str_starts_with($method, 'registerMatchers')) {
                    $this->matchers += $this->{$method}();
                }
            }
        }

        return $this->matchers;
    }
}
