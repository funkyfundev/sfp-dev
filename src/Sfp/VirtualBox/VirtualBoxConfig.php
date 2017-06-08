<?php

namespace Sfp\VirtualBox;

class VirtualBoxConfig
{
    protected $vm_name;
    public $vm_ip;

    public function __construct(array $config)
    {
        if(empty($config)) {
            throw new VirtualBoxException(VirtualBoxException::INVALID_CONFIG_KEY_SUPPLIED);
        }

        foreach($config as $key => $value) {
            if(!property_exists($this, $key)) {
                throw new VirtualBoxException(VirtualBoxException::INVALID_CONFIG_KEY_SUPPLIED);
            }

            $this->{$key} = $value;
        }
    }

    public function getVmName()
    {
        return $this->vm_name;
    }

    public function getVmIp()
    {
        return $this->vm_ip;
    }

}