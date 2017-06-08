<?php

namespace Sfp\VirtualBox;

class VirtualBoxManager
{
    /**
     * Config array for VM
     *
     * @var Object
     */
    protected $config;

    /**
     * Constructor dependency VirtualBoxConfig
     *
     * @param VirtualBoxConfig $config [description]
     */
    public function __construct(VirtualBoxConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Runs shell command
     *
     * @param  string $cmd
     * @return [type] [description]
     */
    protected function cmd($cmd)
    {
        return shell_exec($cmd . ' 2>&1');
    }

    /**
     * Get the VM name from config
     *
     * @return string
     */
    public function getVMName()
    {
        return $this->config->getVmName();
    }

    /**
     * Execute command to start
     *
     * @return void
     */
    public function start()
    {
        $this->cmd(sprintf("VBoxManage startvm %s --type headless", $this->config->getVmName()));
    }

    /**
     * Get the VM's IP property
     *
     * @param  string $key [description]
     * @return bool|array
     */
    function getGuestProperty($key)
    {
        $vm = $this->config->getVmName();
        $output = $this->cmd(sprintf('VBoxManage guestproperty get %s %s', $vm, $key));

        if(preg_match('/^Value: (.+?)$/', $output, $matches) and !empty($matches[1])){
            return $matches[1];
        }

        return false;
    }

    /**
     * Execute shutdown VM
     *
     * @return void
     */
    public function shutdown()
    {
        $vm = $this->config->getVmName();

        $this->vmShutdown($vm);
    }

    /**
     * Get the VM's status
     *
     * @return string vm's status
     */
    public function getStatus()
    {
        $status = $this->getVmInfo($this->config->getVmName());

        if(!isset($status['VMState'])){
            return false;
        }

        return $status['VMState'];
    }

    /**
     * Shutdown VM
     *
     * @param  string     $vm
     * @return string
     */
    private function vmShutdown($vm)
    {
        $this->cmd(sprintf('VBoxManage controlvm %s poweroff', $vm));
    }

    /**
     * Get VM's information
     *
     * @param  string $vm
     * @return array $info
     */
    private function getVmInfo($vm)
    {
        $output = $this->cmd(sprintf('VBoxManage showvminfo %s --machinereadable', $vm));
        $info = [];
        foreach(explode("\n", $output) as $line){
            if(preg_match('/^(.+?)="?(.+?)"?$/', $line, $matches)){
                $info[$matches[1]] = $matches[2];
            }
        }
        return $info;
    }
}