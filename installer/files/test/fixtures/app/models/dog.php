<?php
class Dog extends ActiveRecord
{
    var $acts_as = array('permissionable' => array('cache_in_session'=>true));
}
?>