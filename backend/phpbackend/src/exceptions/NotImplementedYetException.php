<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class NotImplementedYetException
 */
class NotImplementedYetException extends \Exception implements UnavailableExceptionInterface {
    function __construct($message = 'not_implemented_yet_exception') {
        parent::__construct($message);
    }
}
