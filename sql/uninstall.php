<?php
    $sqls=array();
    $sqls[]='DROP TABLE IF EXISTS `'._DB_PREFIX_.'simplevideos`';
    foreach ($sqls as $sql) {
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    }
