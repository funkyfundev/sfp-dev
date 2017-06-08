<?php

namespace Sfp\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Yaml\Yaml;

use Sfp\VirtualBox\VirtualBoxConfig;
use Sfp\VirtualBox\VirtualBoxManager;
use Sfp\VirtualBox\VirtualBoxException;

class VboxCommand extends Command
{
    const VBOX_START = 'start';
    const VBOX_STOP = 'stop';
    const VBOX_STATUS = 'status';
    const VBOX_RESTART = 'restart';
    const VBOX_RAWIP = 'raw-ip';
    const LIST_ACTIONS = 'list-actions';

    protected $output;
    protected $input;

    /**
     * Initial configuration commands
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('dev')
            ->setDescription('SFP Dev environment commands')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Required action for dev machine. <info>[%s]</info>', implode(", ", $this->getAllowedActions())));
    }

    /**
     * Execute commands based on user input
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        $action = $input->getArgument('action');

        if($action != 'raw-ip') {
            $this->showTitle();
        }

        if(!in_array($action, $this->getAllowedActions())) {
            throw new InvalidArgumentException(sprintf('Argument action "%s" is not valid.', $action));
        }

        try{
            $config = new VirtualBoxConfig(Yaml::parse(file_get_contents(APP_CONFIG)));
        }catch(VirtualBoxException $e) {
            throw new InvalidArgumentException('Config not valie. Please check config.yml');
        }

        $vm = new VirtualBoxManager($config);

        switch($action) {
            case 'start':
                $this->vmStart($vm);
            break;
            case 'stop':
                $this->vmStop($vm);
            break;
            case 'status': 
                $this->getVmStatus($vm);
            break;
            case 'restart': 
                $this->vmRestart($vm);
            break;
            case 'raw-ip':
                $this->output->write($vm->getVmIp());
                exit;
            break;
            case 'list-actions':
                $this->listActions($output);
        }
    }

    /**
     * Restart VM machine
     *
     * @param  VirtualBoxManager $vm
     * @return void
     */
    protected function vmRestart(VirtualBoxManager $vm)
    {
        $this->output->writeln("\n<comment>Restarting ...</comment>");

        $this->vmStop($vm);
        $this->vmStart($vm);
    }

    /**
     * Start VM machine
     *
     * @param  VirtualBoxManager $vm
     * @return void
     */
    protected function vmStart(VirtualBoxManager $vm)
    {
        $status = $vm->getStatus();

        if($status != 'running'){
            $this->output->writeln("\n<comment>Starting...</comment>");
            $vm->start();
            $this->output->writeln("<comment>Waiting for VM IP...</comment>");

            sleep(5);

            while(
                ($bridgedIP = $vm->getGuestProperty('/VirtualBox/GuestInfo/Net/0/V4/IP')) == false or
                ($hostonlyIP = $vm->getGuestProperty('/VirtualBox/GuestInfo/Net/1/V4/IP')) == false
            ){
                $this->output->writeln("trying....");
                sleep(2);
            }

            $this->output->writeln("\n<info>Dev VM ready</info>\n");

        }else{
            $this->output->writeln("\n<info>Dev running</info>\n");
        }
    }

    /**
     * Stopping VM
     *
     * @param  VirtualBoxManager $vm
     * @return void
     */
    protected function vmStop(VirtualBoxManager $vm)
    {
        $status = $vm->getStatus();

        if($status == 'saved'){
            $this->output->writeln("<comment>Starting VM due to saved state...</comment>");
            $this->vmStart($vm);
        }

        if($status != 'poweroff' and $status != 'aborted'){
            $this->output->writeln("<comment>Shutting down VM...</comment>");
            $vm->shutdown();
        }

        $this->output->writeln("<comment>Waiting for power off...</comment>");
        while($status != 'poweroff' and $status != 'aborted'){
            $status = $vm->getStatus();
            sleep(5);
        }

        $this->output->writeln("\n<info>VM Stopped</info>\n");
    }

    /**
     * Gets the Vm's status
     *
     * @param  VirtualBoxManager $vm
     * @return void
     */
    protected function getVmStatus(VirtualBoxManager $vm)
    {
        $response = $vm->getStatus();

        if($response === false) {
            $this->output->writeln(sprintf("<error>VM %s not found</error>", $vm->getVmName()));
        }else {
            $this->output->writeln(sprintf("\nVM Status: <info>%s</info>\n", $response));
        }
    }

    /**
     * Show actions command options
     *
     * @param  OutputInterface $output
     * @return void
     */
    protected function listActions(OutputInterface $output)
    {
        $table = new Table($output);
        $table
            ->setHeaders(['Action', 'Description'])
            ->setRows([
                ['start', 'Starts Dev machine'],
                ['stop', 'Stops Dev machine'],
                ['restart', 'Restarts Dev machine'],
                ['status', 'Returns the status of the Dev machine'],
                ['raw-ip', 'Returns the IP config']
            ]);
        $table->render();
    }

    /**
     * Shows the command title fancy title
     *
     * @return void
     */
    protected function showTitle()
    {
        $this->output->writeln([
            SFP_COMMAND_TITLE,
            '',
            'Command: sfp dev start|stop|status|restart|list-actions'
        ]);
    }

    /**
     * Get Allowed actions
     *
     * @return array
     */
    private function getAllowedActions()
    {
        return [
            self::VBOX_STATUS,
            self::VBOX_STOP,
            self::VBOX_START,
            self::VBOX_RESTART,
            self::VBOX_RAWIP,
            self::LIST_ACTIONS
        ];
    }
}