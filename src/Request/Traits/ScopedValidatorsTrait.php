<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Request\Traits;

use InvalidArgumentException;
use Losingbattle\MicroBase\Request\Validators;
use Inhere\Validate\Exception\ArrayValueNotExists;

trait ScopedValidatorsTrait
{
    use \Inhere\Validate\Traits\ScopedValidatorsTrait;

    public function eachValidator(array $values, ...$args): bool
    {
        if (! $validator = array_shift($args)) {
            throw new InvalidArgumentException('must setting a validator for \'each\' validate.');
        }

        foreach ($values as $value) {
            $passed = false;

            if (\is_object($validator) && method_exists($validator, '__invoke')) {
                $passed = $validator($value, ...$args);
            } elseif (\is_string($validator)) {
                // special for required
                if ($validator === 'required') {
                    $passed = ! Validators::isEmpty($value);
                } else {
                    if ($value instanceof ArrayValueNotExists) {
                        continue;
                    }
                    if (isset($this->_validators[$validator])) {
                        $callback = $this->_validators[$validator];
                        $passed = $callback($value, ...$args);
                    } elseif (method_exists($this, $method = $validator . 'Validator')) {
                        $passed = $this->{$method}($value, ...$args);
                    } elseif (method_exists(Validators::class, $validator)) {
                        $passed = Validators::$validator($value, ...$args);

                    // it is function name
                    } elseif (\function_exists($validator)) {
                        $passed = $validator($value, ...$args);
                    } else {
                        throw new InvalidArgumentException("The validator [{$validator}] don't exists!");
                    }
                }
            }

            if (! $passed) {
                return false;
            }
        }

        return true;
    }
}
