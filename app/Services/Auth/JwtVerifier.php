<?php

namespace App\Services\Auth;

use RuntimeException;

class JwtVerifier
{
    public function __construct(private JwksProvider $jwks) {}

    public function validate(string $jwt, ?array $requiredAud = null): array
    {
        [$h,$p,$sig,$rawH,$rawP] = $this->split($jwt);

        if (($h['alg'] ?? null) !== 'RS256') throw new RuntimeException('Unsupported alg');
        $kid = $h['kid'] ?? null; if (!$kid) throw new RuntimeException('Missing kid');

        $publicPem = $this->jwks->getPublicPemByKid($kid);
        $signed = $rawH.'.'.$rawP;

        $ok = openssl_verify($signed, $this->b64d($sig), $publicPem, OPENSSL_ALGO_SHA256);
        if ($ok !== 1) throw new RuntimeException('Invalid signature');

        $skew = (int) config('auth_jwt.clock_skew', 60); $now = time();
        $iss = $p['iss'] ?? null; $aud = $p['aud'] ?? null;
        $exp = $p['exp'] ?? null; $nbf = $p['nbf'] ?? null; $iat = $p['iat'] ?? null;

        if (!$iss || $iss !== config('auth_jwt.issuer')) throw new RuntimeException('Bad iss');
        $expected = $requiredAud ?: config('auth_jwt.default_audiences');

        foreach ((array)$expected as $a) if (!in_array($a, (array)$aud, true)) throw new RuntimeException('Bad aud');
        if (!$exp || ($now - $skew) >= $exp) throw new RuntimeException('Expired');
        if ($nbf && ($now + $skew) < $nbf) throw new RuntimeException('Not yet valid');
        if ($iat && ($iat - $skew) > $now) throw new RuntimeException('Bad iat');

        return ['ok'=>true, 'claims'=>$p, 'kid'=>$kid];
    }

    private function split(string $jwt): array
    {
        $parts = explode('.', $jwt); if (count($parts)!==3) throw new RuntimeException('Malformed');
        [$h64,$p64,$s64] = $parts;
        return [
            json_decode($this->b64d($h64), true) ?: [],
            json_decode($this->b64d($p64), true) ?: [],
            $s64, $h64, $p64
        ];
    }

    private function b64d(string $b64): string
    {
        $p = strlen($b64)%4; if ($p) $b64 .= str_repeat('=', 4-$p);
        return base64_decode(strtr($b64, '-_', '+/'));
    }
}
