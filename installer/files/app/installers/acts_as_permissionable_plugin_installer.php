<?php

class ActsAsPermissionablePluginInstaller extends AkInstaller
{
    function down_1()
    {
        $this->dropTable('permissionable_objects');
        $this->dropTable('permissionable_object_permissions');
    }
    
     function up_1()
    {

        $this->createTable('permissionable_objects','id,
                                        object_type,
                                        object_id,
                                        owner_type,
                                        owner_id,
                                        created_at,
                                        updated_at');
        
        $this->addIndex('permissionable_objects','object_type');
        $this->addIndex('permissionable_objects','owner_type');
        
        $this->createTable('permissionable_object_permissions','id,
                                        permissionable_object_id,
                                        actor_type,
                                        actor_id,
                                        permission,
                                        created_at,
                                        updated_at');
        
        $this->addIndex('permissionable_object_permissions','actor_type,actor_id');
        $this->addIndex('permissionable_object_permissions','UNIQUE permissionable_object_id,actor_type,actor_id,permission','UNQ_permission');
    }
}
?>