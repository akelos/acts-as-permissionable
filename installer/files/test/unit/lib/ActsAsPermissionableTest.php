<?php
require_once(AK_BASE_DIR.DS.'app'.DS.'vendor'.DS.'plugins'.DS.'acts_as_permissionable'.DS.'lib'.DS.'ActsAsPermissionable.php');

class ActsAsPermissionableTest extends AkUnitTest
{
    var $fixtures = array('fishes','cats','dogs','hordes');
    function test_start()
    {
        $this->uninstallAndInstallMigration('ActsAsPermissionablePlugin');
        $this->installAndIncludeModels('Cat,Dog,Fish,Horde');
        $this->includeAndInstatiateModels('Cat,Dog,Fish,Horde,PermissionableObject,PermissionableObjectPermission,PermissionableObjectPermissionsHolder');
        
    }
    function test_setup_permission_objects()
    {
        $salmon_object = PermissionableObject::getObject($this->fishes['salmon']);
        
        $this->assertTrue($salmon_object!=false);
        $this->assertEqual(1,$salmon_object->getId());
        
        $tuna_object = PermissionableObject::getObject($this->fishes['tuna']);
        
        $this->assertTrue($tuna_object!=false);
        $this->assertEqual(2,$tuna_object->getId());

    }
    
    function test_setup_permissions()
    {
        $this->assertFalse($this->cats['hexe']->permissionable->can('eat',$this->fishes['salmon']));
        $this->assertTrue($this->cats['hexe']->permissionable->grant('eat',$this->fishes['salmon']));
        $this->assertTrue($this->cats['hexe']->permissionable->can('eat',$this->fishes['salmon']));
        $this->assertTrue($this->cats['hexe']->permissionable->revoke('eat',$this->fishes['salmon']));
        $this->assertFalse($this->cats['hexe']->permissionable->can('eat',$this->fishes['salmon']));
    }
    
    function test_inherit_permissions_from()
    {
        $this->assertFalse($this->hordes['homies']->permissionable->can('prepare sushi',$this->fishes['tuna']));
        $this->assertFalse($this->cats['hexe']->permissionable->can('prepare sushi',$this->fishes['tuna']));
        $this->assertTrue($this->hordes['homies']->permissionable->grant('prepare sushi',$this->fishes['tuna']));
        
        $cat = $this->Cat->findFirstBy('name','hexe',array('include'=>'horde'));
        $this->assertTrue($cat->permissionable->can('prepare sushi',$this->fishes['tuna']));
        
        $horde = $this->Horde->findFirstBy('name','homies',array('include'=>'cats'));
        $this->assertFalse($horde->permissionable->can('eat',$this->fishes['salmon']));
        
        $this->assertTrue($this->cats['hexe']->permissionable->grant('eat',$this->fishes['salmon']));
        $horde = $this->Horde->findFirstBy('name','homies',array('include'=>'cats'));
        $this->assertTrue($horde->permissionable->can('eat',$this->fishes['salmon']));
    }
    
    function test_owner_can_do_it_all()
    {
        $this->cats['penny']->permissionable->owns($this->fishes['salmon']);
        $this->assertTrue($this->cats['penny']->permissionable->can('sell',$this->fishes['salmon']));
    }
}
?>