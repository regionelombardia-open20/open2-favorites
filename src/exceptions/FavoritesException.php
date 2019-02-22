<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\favorites\exceptions
 * @category   CategoryName
 */

namespace lispa\amos\favorites\exceptions;

/**
 * Class FavoritesException
 * @package lispa\amos\favorites\exceptions
 */
class FavoritesException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n{$this->getFile()}:{$this->getLine()}\n";
    }
}
