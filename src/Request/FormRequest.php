<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Request;

use Hyperf\Contract\ValidatorInterface;
use Hyperf\Context\Context;
use Psr\Container\ContainerInterface;

class FormRequest
{
    /**
     * The container instance.
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function translates(): array
    {
        return [];
    }

    public function scenarios(): array
    {
        return [];
    }

    public function load(array $data = [])
    {
        $this->getValidatorInstance()->load($data);
        return $this;
    }

    public function setScene(string $scene)
    {
        $this->getValidatorInstance()->setScene($scene);
        return $this;
    }

    public function check(): void
    {
        $this->getValidatorInstance()->check();
    }

    public function get($key, $default = null)
    {
        $safeData = $this->getValidatorInstance()->getSafe($key);
        return $safeData ? $safeData : $this->getValidatorInstance()->get($key, $default);
    }

    /**
     * 获取验证后的数据和原始数据合并,主要回写了默认值
     * @return array
     */
    public function getData()
    {
        return array_merge($this->getValidatorInstance()->getSafeData(), $this->getValidatorInstance()->all());
    }

    /**
     * 获取验证的数据.
     * @return array|\stdClass
     */
    public function getSafeData()
    {
        return $this->getValidatorInstance()->getSafeData();
    }

    /**
     * 获取原始数据.
     * @return array
     */
    public function getOriginData()
    {
        return $this->getValidatorInstance()->all();
    }

    /**
     * Get the validator instance for the request.
     * @return BaseValidation
     */
    protected function getValidatorInstance()
    {
        return Context::getOrSet($this->getContextValidatorKey(), function () {
            /* @var BaseValidateTrait $validator */
            return $this->createDefaultValidator();
        });
    }

    /**
     * Get context validator key.
     */
    protected function getContextValidatorKey(): string
    {
        return sprintf('%s:%s', static::class, ValidatorInterface::class);
    }

    private function createDefaultValidator()
    {
        return new BaseValidation($this->rules(), $this->translates(), $this->scenarios(), $this->messages());
    }
}
