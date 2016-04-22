<?php
class Bitbull_Satispay_Model_Logger extends Mage_Core_Model_Abstract
{
    /**
     * Store flag to force logging
     *
     * @var string
     */
    protected $_force = false;

    /**
     * Log file name
     */
    protected $_filename = 'Bitbull_Satispay.log';

    /**
     * @var Mage_Core_Model_Logger
     */
    private $_logger;

    /**
     * Standard constructor.
     */
    public function __construct($parameters)
    {
        if (is_bool($parameters)) {
            $this->_force = $parameters;
        }

        $this->_logger = Mage::getModel('core/logger');
    }

    protected function _log($message, $level, $force)
    {
        if (is_null($force)) {
            $force = $this->_force;
        }

        return $this->_logger->log($message, $level, $this->_filename, $force);
    }

    /**
     * Use to log debug messages.
     *
     * Debugging messages bring extended information about application processing.
     * Such messages usually report calls of important functions along with results they return
     * and values of specific variables or parameters.
     */
    public function debug($message, $force = null)
    {
        return $this->_log($message, Zend_Log::DEBUG, $force);
    }

    /**
     * Use to log informative messages.
     *
     * Informative messages are usually used for reporting significant application progress and stages.
     * Informative messages should not be reported too frequently because they can quickly become noise.
     */
    public function info($message, $force = null)
    {
        return $this->_log($message, Zend_Log::INFO, $force);
    }

    /**
     * Use to log normal but significant condition.
     */
    public function notice($message, $force = null)
    {
        return $this->_log($message, Zend_Log::NOTICE, $force);
    }

    /**
     * Use to log that application encountered warning conditions.
     *
     * Such messages are reported when something unusual happened that is not critical to process,
     * but it would be useful to review this situation to decide if it should be resolved.
     */
    public function warn($message, $force = null)
    {
        return $this->_log($message, Zend_Log::WARN, $force);
    }

    /**
     * Use to log that application encountered error conditions.
     *
     * A problem occurred while processing the current operation.
     * Such a message usually requires the user to interact with the application or research the problem
     * in order to find the reason and resolve it.
     */
    public function err($message, $force = null)
    {
        return $this->_log($message, Zend_Log::ERR, $force);
    }

    /**
     * Use to log that application is in critical conditions.
     *
     * A critical problem occurred while processing the current operation.
     * The application is in a critical state and cannot proceed with the execution of the current operation.
     * In this case, the application usually reports such message and terminates.
     * Such a message usually requires the user to interact with the application or research the problem
     * in order to find the reason and resolve it.
     */
    public function crit($message, $force = null)
    {
        return $this->_log($message, Zend_Log::CRIT, $force);
    }

    /**
     * Use to log that an action must be taken immediately.
     *
     * A very critical problem occurred while processing the current operation.
     * Such a message usually requires the user to interact urgently with the application or research the problem
     * in order to find the reason and resolve it.
     */
    public function alert($message, $force = null)
    {
        return $this->_log($message, Zend_Log::ALERT, $force);
    }

    /**
     * Use to log that system is unusable.
     *
     * The application is in an unusable state and cannot proceed with the execution of the current operation.
     * In this case, the application usually reports such message and terminates.
     * Such a message usually requires the user to interact immediately with the application or research the problem
     * in order to find the reason and resolve it.
     */
    public function emerg($message, $force = null)
    {
        return $this->_log($message, Zend_Log::EMERG, $force);
    }
}