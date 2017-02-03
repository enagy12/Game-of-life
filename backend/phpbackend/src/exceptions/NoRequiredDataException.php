<?php namespace hu\doxasoft\phpbackend\exceptions;

/**
 * Class NoRequiredDataException
 */
class NoRequiredDataException extends \Exception implements BadRequestExceptionInterface {
    function __construct($message = 'no_required_data_exception') {
        parent::__construct($message);
    }
}
