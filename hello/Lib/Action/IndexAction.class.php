<?php
class IndexAction extends Action{
    public function index($name='ThinkPHP') {
        $this->hello    =   'Hello,'.$name.'！';
        echo '111';
        $this->display();
    }
}