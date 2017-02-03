<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class UnknownPathException
 */
class UnknownPathException extends \Exception implements BadRequestExceptionInterface {
    function __construct($message = 'unknown_path_exception') {
        parent::__construct($message);
    }
}
