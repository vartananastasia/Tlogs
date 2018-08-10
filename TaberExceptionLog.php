<?php
/**
 * Created by PhpStorm.
 * User: a.vartan
 * Date: 01.08.2018
 * Time: 12:11
 */

namespace Taber\Podrygka\TaberLogs;

use Bitrix\Main\Diag\Debug;

/**
 * Class TaberExceptionLog
 * @package Taber\Podrygka\TaberLogs
 */
class TaberExceptionLog
{
    /**
     * @var \Exception
     */
    private $_exception;

    /**
     * апись лога в базу данных
     */
    const DATA_BASE_LOG = 1;
    /**
     * запись лога в файл
     */
    const FILE_LOG = 2;
    /**
     * путь к дефолтному файлу логов
     */
    const DEFAULT_LOG_FILE = '_log/default_log.txt';

    /**
     * TaberExceptionLog constructor.
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->_exception = $exception;
        self::write($this);
    }

    /**
     * @param int $log_type
     */
    public function write($log_type = self::DATA_BASE_LOG)
    {
        switch ($log_type) {
            case self::DATA_BASE_LOG:
                LogsTable::writeLogsTable($this);
                break;
            case self::FILE_LOG:
                Debug::dumpToFile(
                    $this->_exception::__toString(),
                    'EXCEPTION_STRING_' . date('d.m.Y_H:i:s'),
                    get_class($this->_exception)::LOG_FILE ?? self::DEFAULT_LOG_FILE
                );
                break;
        }
    }

    /**
     * @return string
     */
    public function getTrace()
    {
        return $this->_exception->getTraceAsString();
    }

    public function getTraceArray(){
        return $this->_exception->getTrace();
    }

    /**
     * @return int|mixed
     */
    public function getCode()
    {
        return $this->_exception->getCode();
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->_exception->getFile();
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->_exception->getLine();
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_exception->getMessage();
    }

    public function getError(){
        return get_class($this->_exception);
    }
}