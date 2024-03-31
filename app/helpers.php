<?php
require_once __DIR__ . '/../app/bootstrap.php';


function renderView($view, $data = [])
{
    extract($data);
    ob_start();
    include __DIR__ . "/Views/$view.php";
    $content = ob_get_clean();
    include __DIR__ . "/Views/layouts/main.php";
}

function asset($path)
{
    return $_ENV['BASE_URL'] . '/assets/' . $path;
}


function encryptCredential($data, $encryptionKey)
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $encryptionKey, 0, $iv);
    return base64_encode($encryptedData . '::' . $iv);
}

function decryptCredential($data, $encryptionKey)
{
    list($encryptedData, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encryptedData, 'aes-256-cbc', $encryptionKey, 0, $iv);
}


function sanitizeInput($input)
{
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}