<?php

#create http header
header('Access-Control-Allow-Origin: * ');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === "OPTIONS") {
    header('HTTP/1.1 200 OK');
    exit();

}
