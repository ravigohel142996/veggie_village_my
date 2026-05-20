<?php

$appDebug = filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN);

ini_set('display_errors', $appDebug ? '1' : '0');
ini_set('display_startup_errors', $appDebug ? '1' : '0');
ini_set('log_errors', '1');
error_reporting($appDebug ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT);

if (!defined('VEGGIE_VILLAGE_EXCEPTION_HANDLER')) {
    define('VEGGIE_VILLAGE_EXCEPTION_HANDLER', true);

    set_exception_handler(function (Throwable $exception) use ($appDebug): void {
        error_log(sprintf('[%s] %s in %s:%d', date('c'), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

        if (!headers_sent()) {
            http_response_code(500);
        }

        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || stripos($acceptHeader, 'application/json') !== false;

        if ($isAjax) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode(['code' => '0', 'msg' => 'Internal server error.']);
            exit;
        }

        echo $appDebug
            ? nl2br(htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'))
            : 'Something went wrong. Please try again later.';
        exit;
    });
}

