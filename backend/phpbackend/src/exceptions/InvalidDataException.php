<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class InvalidDataException
 */
class InvalidDataException extends \Exception implements BadRequestExceptionInterface {
    function __construct($message = 'invalid_data_exception') {
        parent::__construct($message);
    }
}
