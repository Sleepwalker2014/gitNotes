<?php
/**
 * Created by PhpStorm.
 * User: marcel
 * Date: 26.05.17
 * Time: 12:26
 */

namespace php;


use Exception;

class CommandException extends Exception {

    /**
     * CommandException constructor.
     */
    public function __construct () {
        $this->code = 1;
        $this->message = 'An Invalid Command has been executed';
    }
}