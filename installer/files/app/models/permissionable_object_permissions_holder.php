<?php
class PermissionableObjectPermissionsHolder
{
    var $permissions = array();
    
    var $is_owner = false;
    
    function __construct($is_owner = false, $permissions = array())
    {
        $this->permissions = $permissions;
        $this->is_owner = $is_owner;
    }
    
    static function &get(&$object, &$actor)
    {
        if (is_object($object) && $object instanceof AkActiveRecord && is_object($actor) && $actor instanceof AkActiveRecord) {
            $actor_type = $actor->getType();
            $actor_id = $actor->getId();
            $o = &PermissionableObject::getObject($object,array('conditions'=>'_permissionable_object_permissions.actor_type = ? AND _permissionable_object_permissions.actor_id = ?','bind'=>array($actor_type, $actor_id),'include'=>array('permissionable_object_permission')));
            $permissions = array();
            if ($o) {
                foreach($o->permissionable_object_permissions as $perm) {
                    $permissions[] = $perm->permission;
                }
            }
            $permissions_object = new PermissionableObjectPermissionsHolder($o->isOwner($actor), $permissions);
            return $permissions_object;
        } else {
            $permissions_object = new PermissionableObjectPermissionsHolder(false,array());
            return $permissions_object;
        }
    }
    
    function can($permission)
    {
        if ($this->is_owner) return true;
        
        return in_array($permission, $this->permissions);
    }
    
    
}
?>