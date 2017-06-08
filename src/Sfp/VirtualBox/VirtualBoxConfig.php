<?php

namespace Sfp\VirtualBox;

class VirtualBoxConfig
{
    protected $vm_name;
    public $vm_ip;

    /**
     * Constructor for VM config
     *
     * @param array $config
     * @throws VirtualBoxException
     */
    public function __construct(array $config = [])
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

    /**
     * Get the VM name
     *
     * @return string
     */
    public function getVmName()
    {
        return $this->vm_name;
    }

    /**
     * Get VM IP config
     *
     * @return string
     */
    public function getVmIp()
    {
        return $this->vm_ip;
    }

}