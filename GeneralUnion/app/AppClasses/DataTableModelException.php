<?php
/**
 * Creates a specialised exception for problems that arise when dealing with the database. It emits
 * a hashed array suitable for encoding as a JSON string.
 */

namespace App\AppClasses;

/**
 * Creates a specialised exception for problems that arise when dealing with the database. It emits
 * a hashed array suitable for encoding as a JSON string. In particular it adds the property
 * errorCode, which gets its value from error codes emitted by PostgreSQL. errorCode is often a number
 * but can be a string, so it's different from the status code emitted by HTTP exceptions like 404 for a missing page.
 * The client-side error handler is a aware that errorCod is a string and has a switch structure testing for likely
 * errors, such as foreign key constraint violations.
 *
 * @extends Exception
 * @author David Mann
 */
class DataTableModelException extends \Exception implements \Throwable, \JsonSerializable {
    /**
     * Redefines the exception so message isn't optional
     */
    public function __construct($message, $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * A custom string representation of this object
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    /**
     * This returns a hashed array with the following properties:
     * error - this contains the string returned by the getMessage() method
     * errorCode - this contains the integer returned by the getCode() method cast as a string
     * because with PDOException, the codes come from the database and sometimes contain letters.
     * file - this contains the name of the file where the exception was thrown
     * line - this contains the integer value of the line in the file where the exception was thrown
     * trace - this is an array representation of the trace through all the steps that led to the 
     * exception being thrown.
     * @return array
     */
    public function jsonSerialize() {
        $json = [
            /**
             * @var string
             */
            'error' => $this->getMessage(),
            /**
             * @var string
             */
            'errorCode' => (string)$this->getCode(),
            /** 
             * @var string
             */
            'file' => $this->getFile(),
            /**
             * @var int
             */
            'line' => $this->getLine()
        ];
        //if(config('debug')){
            $json['trace'] = $this->getTrace();
        //}
        return $json;
    }
}