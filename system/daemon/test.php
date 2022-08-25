#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * This example shows how to establish a UDP connection.
 *
 * How to run this script:
 *     docker exec -t $(docker ps -qf "name=client") bash -c "./clients/udp.php"
 *
 * Here is the source code of the UDP server:
 * @see https://github.com/deminy/swoole-by-examples/blob/master/examples/servers/udp.php
 */
    
    // var_dump(SWOOLE_SOCK_SYNC);
    


    $omy_conn_workspace = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_SYNC);
    $omy_conn_workspace->connect('127.0.0.1', 8999);

    if($omy_conn_workspace->isconnected()){

            $omy_conn_workspace->send('Hello Swoole!');


    }else{
        // var_dump($omy_conn_workspace->isconnected());
    }

        // var_dump($omy_conn_workspace);
    // $client = new Client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_SYNC);
    // $client->connect(127.0.0.1, 8999);
    // $client->send('Hello Swoole!');
    // echo $client->recv() . "\n";
    // $client->close();
