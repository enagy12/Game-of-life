<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class DataCreateException
 */
class DataCreateException extends \Exception implements UnavailableExceptionInterface {
    function __construct($message = 'data_create_exception') {
        parent::__construct($message);
    }
}
