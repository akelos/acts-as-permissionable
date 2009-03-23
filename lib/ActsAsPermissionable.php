<?php
require_once(AK_LIB_DIR.DS.'AkObject.php');

class ActsAsPermissionable extends AkObject
{
    var $_cache_in_session = false;
    var $_inherit_permissions_from = array();
    var $_object_permissions = array();
    
    var $_instance;
    
    function ActsAsPermissionable(&$ActiveRecordInstance, $options = array())
    {
        $this->_instance = &$ActiveRecordInstance;
        $this->init($options);
    }
    
    function init($options)
    {
        $default_options = array('inherit_permissions_from'=>array(),'cache_in_session'=>false);
        $available_options = array('inherit_permissions_from','cache_in_session');
        $options = array_merge($default_options, $options);
        
        $this->_inherit_permissions_from = Ak::toArray($options['inherit_permissions_from']);
        $this->_cache_in_session = $options['cache_in_session'];
        
        $this->__loadFromSession();
    }
    function grant($permission, &$object)
    {
        if (is_object($object) && $object instanceof AkActiveRecord) {
            if (($o = PermissionableObject::getObject($object))!==false) {
                $res=$o->grant($this->_instance, $permission);
                unset($this->_object_permissions[$object->getType()][$object->getId()]);//[] = new PermissionableObjectPermissionsHolder($o->isOwner($object),array($permission));
                return $res;
            }
        }
        return false;
    }
    function owns(&$object)
    {
        if (is_object($object) && $object instanceof AkActiveRecord) {
            if (($o = PermissionableObject::getObject($object))!==false) {
                $res=$o->setOwner($this->_instance);
                unset($this->_object_permissions[$object->getType()][$object->getId()]);
                return $res;
            }
        }
        return false;
    }
    function revoke($permission, &$object)
    {
        if (is_object($object) && $object instanceof AkActiveRecord) {
            if (($o = PermissionableObject::getObject($object))!==false) {
                unset($this->_object_permissions[$object->getType()][$object->getId()]);
                return $o->revoke($this->_instance, $permission);
            }
        }
        return false;
    }
    function can($permission, &$object)
    {
        if (is_object($object) && $object instanceof AkActiveRecord) {
            $type = $object->getType();
            $id = $object->getId();
            if (!isset($this->_object_permissions[$type])) {
                $this->_object_permissions[$type] = array();
            }
            if (!isset($this->_object_permissions[$type][$id])) {
                $this->_object_permissions[$type][$id][] = &PermissionableObjectPermissionsHolder::get($object, $this->_instance);
                $this->__saveInSession($type,$id);
            }
            if (!empty($this->_inherit_permissions_from)) {
                
                foreach($this->_inherit_permissions_from as $inherit) {
                    if (isset($this->_instance->$inherit)) {
                        if (is_array($this->_instance->$inherit)) {
                            foreach($this->_instance->$inherit as $inherited) {
                                $this->_object_permissions[$type][$id][] = &PermissionableObjectPermissionsHolder::get($object, $inherited);
                            }
                        } else {
                            $this->_object_permissions[$type][$id][] = &PermissionableObjectPermissionsHolder::get($object, $this->_instance->$inherit);
                        }
                    }
                }
            }
            var_dumP($this->_object_permissions);
            foreach($this->_object_permissions[$type][$id] as $perm) {
                if ($perm->can($permission)) return true;
            }
            return false;
        } else {
            return false;
        }
    }
    
    function __saveInSession($type, $id) {
        if ($this->_cache_in_session) {
            $_SESSION['__permissionable__'][$this->_instance->getType()][$this->_instance->getId()][$type][$id] = $this->_object_permissions[$type][$id];
        }
    }
    
    function __loadFromSession()
    {
        if ($this->_cache_in_session && isset($_SESSION['__permissionable__'])) {
            $this->_object_permissions = @$_SESSION['__permissionable__'][$this->_instance->getType()][$this->_instance->getId()];
        }
    }
}
?>