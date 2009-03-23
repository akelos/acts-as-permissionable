<?php
class Horde extends ActiveRecord
{
    var $hasMany = array('cats');
    var $acts_as = array('permissionable' => array('cache_in_session'=>true,'inherit_permissions_from'=>'cats'));
}
?>