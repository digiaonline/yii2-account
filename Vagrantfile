# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  # virtualbox config
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  # vmware fusion config
  config.vm.provider "vmware_fusion" do |v, o|
    o.vm.box = "precise64_vmware"
    o.vm.box_url = "http://files.vagrantup.com/precise64_vmware.box"
  end

  # vmware workstation config
  config.vm.provider "vmware_workstation" do |v, o|
    o.vm.box = "precise64_vmware"
    o.vm.box_url = "http://files.vagrantup.com/precise64_vmware.box"
  end

  # set the hostname
  config.vm.hostname = "account-test"

  # enable port forwarding
  config.vm.network "forwarded_port", guest: 80, host: 8000

  # set up provisioning
  config.vm.provision "shell", :path => "scripts/provision.sh"

  # configure synced folders
  config.vm.synced_folder ".", "/vagrant"

end
