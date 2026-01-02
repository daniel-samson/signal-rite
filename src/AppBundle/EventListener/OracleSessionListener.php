<?php

namespace AppBundle\EventListener;

use Doctrine\DBAL\Event\ConnectionEventArgs;

class OracleSessionListener
{
    public function postConnect(ConnectionEventArgs $args)
    {
        $connection = $args->getConnection();

        // Set Oracle date/timestamp formats to ISO format that PHP understands
        $connection->exec("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
        $connection->exec("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS.FF'");
    }
}
