<?php
/**
 * Created by PhpStorm.
 * User: a.vartan
 * Date: 01.08.2018
 * Time: 12:25
 */

namespace Taber\Podrygka\TaberLogs;

use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;

/**
 * Class LogsTable
 * @package Taber\Podrygka\TaberExceptionLog
 */
class LogsTable
{
    /**
     * таблица хранения логов
     */
    const TABLE_NAME = 'taber_logs';

    /**
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    private static function createLogsTable()
    {
        Application::getConnection()->query(
            "create table if not exists " . self::TABLE_NAME . " (
            id int(11) NOT NULL AUTO_INCREMENT,
            code int(5) not null default 0,  
            line int(5) not null default 0,  
            message varchar(3000) not null default '', 
            trace varchar(3000) not null default '', 
            file varchar(255) not null default '',            
            error varchar(255) not null default '',            
            created timestamp not null default current_timestamp,
            primary key (id));");
    }

    /**
     * @param TaberExceptionLog $taberLog
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function insertInLogsTable(TaberExceptionLog $taberLog)
    {
        Application::getConnection()->query(
            'INSERT INTO ' . self::TABLE_NAME .
            ' (code, line, message, trace, file, error) VALUES (' . $taberLog->getCode() .
            ',"' . $taberLog->getLine() . '","' .
            self::clearTextForInsert($taberLog->getMessage()) . '","' .
            self::clearTextForInsert($taberLog->getTrace()) . '","' .
            self::clearTextForInsert($taberLog->getFile()) . '","' .
            self::clearTextForInsert($taberLog->getError()) . '");'
        );
    }


    private static function clearTextForInsert($str)
    {
        return str_replace('\\', '/', trim($str));
    }

    public function writeLogsTable(TaberExceptionLog $taberLog, $last_try = false)
    {
        try {
            self::insertInLogsTable($taberLog);
        } catch (SqlQueryException $e) {
            if (!$last_try) {
                self::createLogsTable();  // создаем таблицу логов
                self::writeLogsTable($taberLog, true);  // пытаемся писать лог повторно
            }else{
                // todo: выбрасывать исключение
            }
        }
    }

    /**
     * @return array
     */
    public static function getLogsTableFields()
    {
        return [
            'code',  // код ошибки
            'line',  // строка ошибки
            'message',  // сообщение
            'trace',  // Stack trace - файловая вложенность ошибки
            'file',  // файл в котором ошибка возникла
            'error',  // класс ошибки
            'created',  // дата возникновения в timestamp
        ];
    }
}