<?php

declare(strict_types=1);
namespace Losingbattle\MicroBase\Request;

trait UserValidateTrait
{
    public function getUserId()
    {
        return $this->get('user_id');
    }

    public function getUserName()
    {
        return $this->get('user_name');
    }

    public function getUserIp()
    {
        return $this->get('user_ip', '');
    }

    public function getSid()
    {
        return $this->get('sid');
    }

    public function getProvince()
    {
        return $this->get('province');
    }

    public function getEparchy()
    {
        return $this->get('eparchy');
    }

    public function getUserRules(): array
    {
        return [
            ['user_id,user_name,sid', 'required'],
            ['province', 'number'],
            ['eparchy', 'number'],
        ];
    }

    public function getUserScenariosValues()
    {
        return ['user_id', 'user_name', 'sid', 'province', 'eparchy'];
    }
}
