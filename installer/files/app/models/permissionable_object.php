<?php
Ak::import('PermissionableObjectPermission');
class PermissionableObject extends ActiveRecord
{
    var $hasMany = array('PermissionableObjectPermissions'=>array('handler_name'=>'permissionable_object_permission','dependent'=>'destroy','foreign_key'=>'permissionable_object_id'));
    
    function __construct()
    {
        $args = func_get_args();
        $this->init($args);
        $this->permissions = new PermissionableObjectPermission();
    }
    /**
     * Enter description here...
     *
     * @param unknown_type $object
     * @param unknown_type $params
     * @return PermissionableObject
     */
    static function &getObject(&$object, $params = array())
    {
        $false = false;
        if (is_object($object) && $object instanceof AkActiveRecord) {
            $object_type = $object->getType();
            $object_id = $object->getId();
            $me = new PermissionableObject();
            $conditions = 'object_type = ? AND object_id = ?';
            $binds = array($object_type,$object_id);
            $params['conditions'] = !empty($params['conditions'])? $conditions.' AND '.$params['conditions'] : $conditions;
            $params['bind'] = array_merge($binds, !empty($params['bind'])?$params['bind']:array());
            $o = &$me->find('first', $params);
            if (!$o) {
                $o = &$me->findFirstBy('object_type AND object_id',$object_type,$object_id);
                if (!$o) {
                    $o = new PermissionableObject(array('object_type'=>$object_type,'object_id'=>$object_id));
                    $o->save();
                }
            }
            return $o;
        } else {
            return $false;
        }
    }
    function setOwner(&$owner)
    {
        if (is_object($owner) && $owner instanceof AkActiveRecord) {
            $this->set('owner_id',$owner->getId());
            $this->set('owner_type',$owner->getType());
            $this->save();
        } else {
            trigger_error(Ak::t('Cannot set owner, owner is not a valid object'));
        }
    }
    
    function isOwner(&$actor)
    {
        if (is_object($actor) && $actor instanceof AkActiveRecord) {
            return $this->get('owner_id') == $actor->getId() && $this->get('owner_type') == $actor->getType();
        } else {
            return false;
        }
    }
    
    function grant(&$actor, $permission)
    {
        if ($this->isOwner($actor)) {
            return true;
        } else if (is_object($actor) && $actor instanceof AkActiveRecord) {
            $p = $this->permissions->findFirstBy('permissionable_object_id AND actor_id AND actor_type AND permission',
                                                                     $this->getId(),$actor->getId(),$actor->getType(),$permission);
            if (!$p) {
                $p = $this->permissions->create(array('permissionable_object_id'=>$this->getId(),
                                                                         'actor_id'=>$actor->getId(),
                                                                         'actor_type'=>$actor->getType(),
                                                                         'permission'=>$permission));
                
            }
            return $p!==false;
        } else {
            return false;
        }
    }
    
    function revoke(&$actor, $permission)
    {
        if ($this->isOwner($actor)) {
            return false;
        } else if (is_object($actor) && $actor instanceof AkActiveRecord) {
            $p = $this->permissions->findFirstBy('permissionable_object_id AND actor_id AND actor_type AND permission',
                                                                     $this->getId(),$actor->getId(),$actor->getType(),$permission);
            if ($p) {
                return $p->destroy();
            }
            return false;
        } else {
            return false;
        }
    }
    
    function can(&$actor, $permission)
    {
        if (is_object($actor) && $actor instanceof AkActiveRecord) {
            $p = $this->permissions->findFirstBy('permissionable_object_id AND actor_id AND actor_type AND permission',
                                                                     $this->getId(),$actor->getId(),$actor->getType(),$permission);
            return $p!==false;
        } else {
            return false;
        }
    }
}
?>