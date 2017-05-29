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
     *
     * @param string $gitLogErrorCode
     */
    public function __construct ($gitLogErrorCode) {
        echo $gitLogErrorCode;
        if ($gitLogErrorCode === 128) {
            $this->message = 'Wechseln Sie in ein GIT-Verzeichnis oder geben sie mittels --repository ein valides GIT-Verzeichnis an';
        }
    }
}