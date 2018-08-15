<?php

class SQLconnection
{
    function ConnectSQL() {
        $config = "mysql:host=localhost;dbname=st2014";
        $username="st2014";
        $password="progress";

        return new PDO($config, $username, $password);
    }
}