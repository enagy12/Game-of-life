<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class DataNotFoundException
 */
class DataNotFoundException extends \Exception implements NotFoundExceptionInterface  {
    function __construct($message = 'data_not_found_exception') {
        parent::__construct($message);
    }
}
