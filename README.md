# sfp-dev - SFP VM Helper

Local VM helper for sfp dev to:
  - start - starts VM machine
  - stop - stops VM machine
  - restart - restarts VM machine
  - ssh - ssh to VM machine

# Requirements
  - Install composer https://getcomposer.org/
  - PHP must be installed
  - Home Directory must have *Applications* folder
  - VirtualBox

# Installation
Clone repo inside *~/Applications* folder
```sh
$ git clone git@github.com:francisalvinbarretto/sfp-dev.git ~/Applications/
```
Install components via composer
```sh
~/Applications/sfp-dev$ composer install
```

# Edit config.yml file
The *sfp-dev/config.yml* contains information of your VM machine. Information is needed and used to identify which machine should be controlled by the helper.

Configurable values
  * vm_name - The name of your VM VirtualBox machine
  * vm_ip - The IP assigned to your VM VirtualBox Machine

# Configure alias
Add the alias in your ~/.bash_profile
```sh
alias dev='~/Applications/sfp-dev/run.sh'
```
Then execute this command to make the alias avaible in your current terminal session
```sh
$ source ~/.bash_profile
```

# And you're ready!
Show helper command list and options
```sh
$ dev --help
```
Show helper's available actions
```sh
$ dev list-actions
```