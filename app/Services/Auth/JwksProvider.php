<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class JwksProvider
{
    public function getJwks(bool $forceRefresh = false): array
    {
        $key = 'ext_jwks_cache';
        $ttl = (int) config('auth_jwt.jwks_cache_ttl', 900);

        if ($forceRefresh) Cache::forget($key);

        return Cache::remember($key, $ttl, function () {
            $url = config('auth_jwt.jwks_url');
            $resp = Http::timeout(5)->get($url);
            if (!$resp->ok()) throw new RuntimeException("Failed JWKS: {$resp->status()}");
            return $resp->json();
        });
    }

    public function getPublicPemByKid(string $kid): string
    {
        $pem = $this->searchPem($this->getJwks(), $kid);
        if (!$pem) $pem = $this->searchPem($this->getJwks(true), $kid); // rotação
        if (!$pem) throw new RuntimeException("No key for kid={$kid}");
        return $pem;
    }

    private function searchPem(array $jwks, string $kid): ?string
    {
        $keys = $jwks['keys'] ?? [];
        foreach ($keys as $k) {
            if (($k['kid'] ?? null) === $kid && ($k['kty'] ?? '') === 'RSA') {
                $n = $k['n'] ?? null; $e = $k['e'] ?? null;
                if ($n && $e) return $this->jwkToPem($n, $e);
            }
        }
        return null;
    }

    private function b64uDec(string $d): string {
        $r = strlen($d) % 4; if ($r) $d .= str_repeat('=', 4 - $r);
        return base64_decode(strtr($d, '-_', '+/'));
    }

    /** JWK (n,e) -> SubjectPublicKeyInfo (PEM) — conforme JWK/JWS. */
    private function jwkToPem(string $nB64u, string $eB64u): string
    {
        $n = ltrim($this->b64uDec($nB64u), "\x00");
        $e = ltrim($this->b64uDec($eB64u), "\x00");

        $seq = $this->asn1Sequence($this->asn1Integer($n) . $this->asn1Integer($e));
        $bitString = "\x03" . $this->asn1Length(strlen("\x00" . $seq)) . "\x00" . $seq;

        $algId = $this->asn1Sequence(
            $this->asn1Oid([1,2,840,113549,1,1,1]) . "\x05\x00"
        );
        $spki = $this->asn1Sequence($algId . $bitString);

        return "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($spki), 64, "\n") .
            "-----END PUBLIC KEY-----\n";
    }

    // ASN.1 helpers
    private function asn1Length(int $len): string {
        if ($len <= 0x7F) return chr($len);
        $tmp = ltrim(pack('N',$len), "\x00");
        return chr(0x80|strlen($tmp)).$tmp;
    }
    private function asn1Integer(string $x): string {
        if (ord($x[0]) > 0x7F) $x = "\x00".$x;
        return "\x02".$this->asn1Length(strlen($x)).$x;
    }
    private function asn1Sequence(string $x): string {
        return "\x30".$this->asn1Length(strlen($x)).$x;
    }
    private function asn1Oid(array $oid): string {
        $first = 40*$oid[0] + $oid[1];
        $rest = array_slice($oid,2);
        $enc = chr($first);
        foreach ($rest as $v) $enc .= $this->asn1Base128Int($v);
        return "\x06".$this->asn1Length(strlen($enc)).$enc;
    }
    private function asn1Base128Int(int $v): string {
        $res=''; do { $res=chr($v&0x7F).$res; $v>>=7; } while($v>0);
        $b=str_split($res); for($i=0;$i<count($b)-1;$i++) $b[$i]=chr(ord($b[$i])|0x80);
        return implode('',$b);
    }
}
