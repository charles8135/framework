<?php
/**
 * 
 * Abstract log class.
 * 
 * @package Bull.Log
 * 
 * @author Gu Weigang <guweigang@baidu.com>
 * 
 */
abstract class Bull_Log_Abstract
{
    /**
     * 
     * The event types this instance will recognize, default '*'.
     * 
     * If you try to save an event type not in this list, the adapter
     * should not record it.
     * 
     * @var array
     * 
     * @see Bull_Log_Abstract::save()
     * 
     */
    protected $_events = array('*');

    /**
     *
     * Microtime Use timestamps with decimal microseconds.
     *
     * @var bool
     *
     */
    protected $_microtime = false;
    
    /**
     *
     * Constructor.
     *
     * @param $events string|array events The event types this instance
     *        should recognize; a comma-separated string of events, or
     *        a sequential array.  Default is all events ('*').
     * 
     * @param $microtime bool microtime Use timestamps with decimal microseconds.
     *        Default false.
     * 
     */
    protected function __construct($events = '*', $microtime = false)
    {
        $this->preConstruct();
        $this->setEvents($events);
        $this->setMicrotime($microtime);
        $this->postConstruct();
    }

    /**
     *
     * Hook before construct
     *
     */
    protected function preConstruct() {}

    /**
     *
     * Hook after construct
     *
     */
    protected function postConstruct() {}

    /**
     *
     * Set time format.
     *
     */
    protected function setMicrotime($microtime)
    {
        if ($microtime == true) {
            $this->_microtime = $microtime;
        }
    }
    
    /**
     * 
     * Saves (writes) an event and message to the log.
     * 
     * {{code: php
     *     $log->save('class_name', 'info', 'informational message');
     *     $log->save('class_name', 'critical', 'critical message');
     *     $log->save('class_name', 'my special event type', 'describing the event');
     * }}
     * 
     * @param string $class The class name logging the event.
     * 
     * @param string $event The event type (typically 'debug', 'info',
     * 'notice', 'severe', 'critical', etc).
     * 
     * @param string $descr A text description of the event.
     * 
     * @return mixed Boolean false if the event was not saved, or a
     * non-empty value if the event was saved (typically boolean true).
     * 
     */
    public function save($class, $event, $descr)
    {
        $save = in_array($event, $this->_events) ||
                in_array('*', $this->_events);
        
        if ($save) {
            return $this->_save($class, $event, $descr);
        } else {
            return false;
        }
    }
    
    /**
     * 
     * Gets the list of events this adapter recognizes.
     * 
     * @return array The list of recognized events.
     * 
     */
    public function getEvents()
    {
        return $this->_events;
    }
    
    /**
     * 
     * Sets the list of events this adapter recognizes.
     * 
     * @param array|string $list The event types this instance
     * should recognize; a comma-separated string of events, or
     * a sequential array.
     * 
     * @return void
     * 
     */
    public function setEvents($list)
    {
        if (is_string($list)) {
            $list = explode(',', $list);
            foreach ($list as $key => $val) {
                $list[$key] = trim($val);
            }
        }
        
        $this->_events = $list;
    }
    
    /**
     * 
     * Gets the current timestamp in ISO-8601 format (yyyy-mm-ddThh:ii:ss).
     * 
     * If the 'microtime' config option is true, the seconds have a decimal
     * portion appended.
     * 
     * @return string The current timestamp.
     * 
     */
    protected function _getTime()
    {
        $time = date('Y-m-d\TH:i:s');
        if ($this->_microtime) {
            // strip the "0" from "0.123456" and append
            $time .= substr((float) microtime(), 1);
        }
        return $time;
    }
    
    /**
     * 
     * Support method to save (write) an event and message to the log.
     * 
     * @param string $class The class name saving the message.
     * 
     * @param string $event The event type (for example 'info' or 'debug').
     * 
     * @param string $descr A description of the event. 
     * 
     * @return mixed Boolean false if the event was not saved (usually
     * because it was not recognized), or a non-empty value if it was
     * saved.
     * 
     */
    abstract protected function _save($class, $event, $descr);
}
