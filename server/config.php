<?php

define('DEVELOPMENT_MODE',true);

$config = [
    "dbHost" => "localhost",
    "dbName" => DEVELOPMENT_MODE ? "atk_db" : "atk_db",
    "dbUser" => DEVELOPMENT_MODE ? "root" : "usr_atkreact_db",
    "dbPass" => DEVELOPMENT_MODE ? "" : "",

    "mailHost" => DEVELOPMENT_MODE ? "mail.wun.com.tr" : "mail.wun.com.tr",
    "mailName" => DEVELOPMENT_MODE ? "info@wun.com.tr" : "info@wun.com.tr",
    "mailPass" => DEVELOPMENT_MODE ? "" : "",
    "mailPort" => DEVELOPMENT_MODE ? "465" : "465",
    "mailSmtp" => true,
    
    "mailDevelopment" => "kutkire@gmail.com",
    "JWT_TOKEN_SECRET_KEY" => "ATK-1971-2003",
    "cpanel" => [
        "domain" => "wun.com.tr",
        "username" => "wuncomtr",
        "password" => "", //cpsess6538043985
        "port" => 2083,
        "whmPort" => 2087
    ],

    "saltToken" => "a70cfc57593c77c7c0049f19e5aa1ede",
    
 

    
];

?>