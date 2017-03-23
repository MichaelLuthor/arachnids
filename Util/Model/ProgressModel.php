<?php
namespace Arachids\Util\Model;
use Arachids\Lib\ActiveRecord\Model;
/**
 * @property string $id
 * @property string $type
 * @property string $target
 * @property string $status
 * @property string $data
 * @property string $message
 * @property string $start_at
 * @property string $end_at
 * @author michaelluthor
 */
class ProgressModel extends Model {
    static $primary_key = 'id';
    static $table_name = 'progress';
    static $connection = 'spider_db';
    
    /** @return void */
    public static function createTable () {
        $model = new ProgressModel();
        $model->connection()->query('CREATE TABLE "progress" (
            "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            "type" TEXT,
            "target" TEXT,
            "status" TEXT,
            "data" TEXT,
            "message" TEXT,
            "start_at" TEXT,
            "end_at" TEXT
         )');
    }
}