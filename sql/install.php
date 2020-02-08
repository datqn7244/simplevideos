<?php
    $sqls=array();
    $sqls[]='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'simplevideos` (
        `id_product` INT(11) NOT NULL,
        `id_video` TEXT NOT NULL,
        `enable` INT(1) NOT NULL,
        `date_add` DATETIME NOT NULL,
        PRIMARY KEY (id_product))
        ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET = UTF8';
foreach ($sqls as $sql) {
    if (!Db::getInstance()->execute($sql)) {
        return false;
    }
}
