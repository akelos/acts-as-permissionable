<?php
class DogInstaller extends AkInstaller
{
    function up_1()
    {
        $this->createTable('dogs','id,name');
    }
    
    function down_1()
    {
        $this->dropTable('dogs');
    }
}
?>