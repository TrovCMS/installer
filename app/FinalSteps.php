<?php

namespace App;

class FinalSteps
{
    public $errors = [];

    public function add(string $response): void
    {
        $this->errors[] = $response;
    }

    public function all(): array
    {
        return $this->errors;
    }
}
