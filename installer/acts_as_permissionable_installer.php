<?php


class ActsAsPermissionableInstaller extends AkPluginInstaller
{

    function up_1()
    {
        $this->runMigration();
        echo "\n\nInstallation completed\n";
    }
    
    function runMigration()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'acts_as_permissionable_plugin_installer.php');
        $Installer =& new ActsAsPermissionablePluginInstaller();

        echo "Running the acts_as_permissionable plugin migration\n";
        $Installer->install();
    }

    function down_1()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'acts_as_permissionable_plugin_installer.php');
        $Installer =& new ActsAsPermissionablePluginInstaller();
        echo "Uninstalling the acts_as_permissionable plugin migration\n";
        $Installer->uninstall();
    }

}
?>