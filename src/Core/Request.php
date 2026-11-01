<?php

namespace App\Core;

/**
 * HTTP Request & Parameter Utility
 * 
 * Provides type-safe parameter extraction, validation, and normalization
 * across query strings, POST bodies, and structured parameter arrays.
 */
class Request {

    /**
     * Resolves the parameter data source: provided array or combined PHP superglobals.
     */
    private static function resolveSource(array|string $sourceOrKey): array {
        if (is_array($sourceOrKey)) {
            return $sourceOrKey;
        }
        return array_merge($_REQUEST, $_GET, $_POST);
    }

    /**
     * Extracts an integer parameter with default fallback.
     * Supports both Request::getInt($array, 'key', 0) and Request::getInt('key', 0)
     */
    public static function getInt(array|string $sourceOrKey, string|int|null $keyOrDefault = null, ?int $default = 0): ?int {
        $source = self::resolveSource($sourceOrKey);
        $key = is_array($sourceOrKey) ? (string)$keyOrDefault : (string)$sourceOrKey;
        $def = is_array($sourceOrKey) ? $default : (is_int($keyOrDefault) ? $keyOrDefault : (is_null($keyOrDefault) ? null : $default));

        if (!isset($source[$key]) || !is_numeric($source[$key])) {
            return $def;
        }
        return (int)$source[$key];
    }

    /**
     * Extracts a trimmed string parameter with default fallback.
     * Supports both Request::getString($array, 'key', '') and Request::getString('key', '')
     */
    public static function getString(array|string $sourceOrKey, string $keyOrDefault = '', string $default = ''): string {
        $source = self::resolveSource($sourceOrKey);
        $key = is_array($sourceOrKey) ? $keyOrDefault : (string)$sourceOrKey;
        $def = is_array($sourceOrKey) ? $default : $keyOrDefault;

        if (!isset($source[$key]) || !is_string($source[$key])) {
            return $def;
        }
        $val = trim($source[$key]);
        return $val !== '' ? $val : $def;
    }

    /**
     * Extracts a string parameter validated against an allowed whitelist.
     * Supports both Request::getEnum($array, 'key', $allowed, $default) and Request::getEnum('key', $allowed, $default)
     */
    public static function getEnum(array|string $sourceOrKey, string|array $keyOrAllowed, array|string $allowedOrDefault = '', string $default = ''): string {
        $source = self::resolveSource($sourceOrKey);
        if (is_array($sourceOrKey)) {
            $key = (string)$keyOrAllowed;
            $allowed = is_array($allowedOrDefault) ? $allowedOrDefault : [];
            $def = $default;
        } else {
            $key = (string)$sourceOrKey;
            $allowed = is_array($keyOrAllowed) ? $keyOrAllowed : [];
            $def = is_string($allowedOrDefault) ? $allowedOrDefault : '';
        }

        $val = self::getString($source, $key, $def);
        return in_array($val, $allowed, true) ? $val : $def;
    }

    /**
     * Extracts a list of non-empty strings from an array or single parameter,
     * supporting multiple fallback keys and ignoring placeholder values like 'all'.
     *
     * @param array|string $sourceOrKeys
     * @param string|array|null $keys
     * @param array $ignored Values to exclude (defaults to ['all', ''])
     * @return array<string>
     */
    public static function getStringList(array|string $sourceOrKeys, $keys = null, array $ignored = ['all', '']): array {
        if (is_array($sourceOrKeys) && $keys !== null) {
            $source = $sourceOrKeys;
            $targetKeys = is_array($keys) ? $keys : [$keys];
        } else {
            $source = array_merge($_REQUEST, $_GET, $_POST);
            $targetKeys = is_array($sourceOrKeys) ? $sourceOrKeys : [$sourceOrKeys];
        }

        $raw = null;
        foreach ($targetKeys as $k) {
            if (isset($source[$k])) {
                $raw = $source[$k];
                break;
            }
        }

        if ($raw === null) {
            return [];
        }

        $items = is_array($raw) ? $raw : [$raw];
        $result = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $trimmed = trim($item);
                if ($trimmed !== '' && !in_array($trimmed, $ignored, true)) {
                    $result[] = $trimmed;
                }
            }
        }
        return array_values(array_unique($result));
    }

    /**
     * Checks if current HTTP request method is POST.
     */
    public static function isPost(): bool {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Sends a JSON response with status code and terminates execution.
     */
    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
