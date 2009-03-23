<?php
class FishInstaller extends AkInstaller
{
    function up_1()
    {
        $this->createTable('fish','id,name');
    }
    
    function down_1()
    {
        $this->dropTable('fish');
    }
}
?>