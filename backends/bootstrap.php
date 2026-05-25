<?php

if (!function_exists('vv_is_debug_enabled')) {
    function vv_is_debug_enabled(): bool
    {
        $raw = getenv('APP_DEBUG');
        if ($raw === false) {
            return false;
        }

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('vv_debug_bootstrap')) {
    function vv_debug_bootstrap(): void
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }
        $initialized = true;

        error_reporting(E_ALL);
        ini_set('log_errors', '1');
        ini_set('error_log', 'php://stderr');

        if (vv_is_debug_enabled()) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        } else {
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
        }
    }
}

if (!function_exists('vv_format_exception')) {
    function vv_format_exception(Throwable $exception): string
    {
        return sprintf(
            '%s: %s in %s on line %d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
    }
}

if (!function_exists('vv_log_exception')) {
    function vv_log_exception(Throwable $exception): void
    {
        error_log('[VeggieVillage] ' . vv_format_exception($exception));
    }
}

if (!function_exists('vv_build_error_response')) {
    function vv_build_error_response(
        string $genericMessage,
        Throwable $exception,
        bool $json = false
    ): string {
        vv_log_exception($exception);
        if (!vv_is_debug_enabled()) {
            return $genericMessage;
        }

        $details = vv_format_exception($exception);
        return $json ? $details : nl2br(htmlspecialchars($details, ENT_QUOTES, 'UTF-8'));
    }
}

vv_debug_bootstrap();

