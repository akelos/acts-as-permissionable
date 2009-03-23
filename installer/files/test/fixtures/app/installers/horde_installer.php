<?php
class HordeInstaller extends AkInstaller
{
    function up_1()
    {
        $this->createTable('hordes','id,name');
    }
    
    function down_1()
    {
        $this->dropTable('hordes');
    }
}
?>