<?php
class CatInstaller extends AkInstaller
{
    function up_1()
    {
        $this->createTable('cats','id,name,horde_id');
    }
    
    function down_1()
    {
        $this->dropTable('cats');
    }
}
?>