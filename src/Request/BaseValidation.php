<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Request;

use Losingbattle\MicroBase\Constants\Code;
use Losingbattle\MicroBase\Exception\ParamErrorException;

class BaseValidation
{
    use BaseValidateTrait;

    protected $data = [];

    public function __construct(
        array $rules = [],
        array $translates = [],
        array $scenarios = [],
        array $messages = []
    ) {
        $this
            ->setRules($rules)
            ->setScenarios($scenarios)
            ->setTranslates($translates)
            ->setMessages($messages);
    }

    public function load(array $data = [], bool $reset = true): self
    {
        if ($reset && $this->_validated === true) {
            $this->resetValidation();
        }
        $this->data = $data;
        return $this;
    }

    public function check(): void
    {
        $this->validate();
        if ($this->firstError()) {
            throw new ParamErrorException($this->firstError(), Code::ERROR_PARAMS);
        }
    }
}
