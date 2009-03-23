<?php
class Cat extends ActiveRecord
{
    var $belongsTo='horde';
    var $acts_as = array('permissionable'=>array('inherit_permissions_from'=>'horde'));
}
?>