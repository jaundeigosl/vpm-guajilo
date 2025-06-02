<?php

namespace App\helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static string $secretKey = '';

    private static function initSecret(): void
    {
        self::$secretKey = $_ENV['JWT_SECRET'] ?? 'fallback_secret';
    }

    public static function generateToken(array $payload, int $minutes = 60): string
    {
        if (!self::$secretKey) self::initSecret();

        $issuedAt = time();
        $expire = $issuedAt + ($minutes * 60);

        return JWT::encode([
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $payload
        ], self::$secretKey, 'HS256');
    }

    public static function verifyToken(string $jwt): object
    {
        if (!self::$secretKey) self::initSecret();

        return JWT::decode($jwt, new Key(self::$secretKey, 'HS256'));
    }
}
