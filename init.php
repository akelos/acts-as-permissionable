<?php

class ActsAsPermissionablePlugin extends AkPlugin
{
    function load()
    {
        require_once($this->getPath().DS.'lib'.DS.'ActsAsPermissionable.php');
    }
}

?>