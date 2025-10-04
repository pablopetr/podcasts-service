<?php

use App\Models\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function genRsaKeypair(): array
{
    $res = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($res, $privPem);
    $details = openssl_pkey_get_details($res);
    $pubPem = $details['key'];
    $n = $details['rsa']['n'];
    $e = $details['rsa']['e'];

    return [$privPem, $pubPem, $n, $e];
}

function b64u(string $bin): string
{
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function jwksFromRsa(string $kid, string $nBin, string $eBin): array
{
    return [
        'keys' => [[
            'kty' => 'RSA',
            'kid' => $kid,
            'use' => 'sig',
            'alg' => 'RS256',
            'n' => b64u($nBin),
            'e' => b64u($eBin),
        ]],
    ];
}

function signJwtRS256(array $header, array $payload, string $privPem): string
{
    $h = b64u(json_encode($header, JSON_UNESCAPED_SLASHES));
    $p = b64u(json_encode($payload, JSON_UNESCAPED_SLASHES));
    $input = $h.'.'.$p;
    openssl_sign($input, $sig, $privPem, OPENSSL_ALGO_SHA256);

    return $input.'.'.b64u($sig);
}

beforeEach(function () {
    Cache::flush();

    config()->set('auth_jwt.issuer', 'http://localhost');
    config()->set('auth_jwt.jwks_cache_ttl', 60);
    config()->set('auth_jwt.default_audiences', ['podcasts']);

    $this->jwksUrl = 'http://auth.test/.well-known/jwks.json';
    config()->set('auth_jwt.jwks_url', $this->jwksUrl);

    [$privPem, $pubPem, $nBin, $eBin] = genRsaKeypair();
    $this->privPem = $privPem;
    $this->kid = (string) Str::uuid();

    $jwks = jwksFromRsa($this->kid, $nBin, $eBin);
    Http::fake([
        $this->jwksUrl => Http::response($jwks, 200),
    ]);
});

function makeAccessToken(string $privPem, string $kid, array $claimsOverrides = []): string
{
    $now = time();
    $payload = array_merge([
        'iss' => config('auth_jwt.issuer'),
        'aud' => ['podcasts'],
        'sub' => '3',
        'ver' => 1,
        'scope' => ['admin', 'podcasts:write', 'podcasts:read'],
        'membership' => 'premium',
        'iat' => $now,
        'nbf' => $now,
        'exp' => $now + 600,
    ], $claimsOverrides);

    $header = ['alg' => 'RS256', 'typ' => 'JWT', 'kid' => $kid];

    return signJwtRS256($header, $payload, $privPem);
}

it('creates a show with valid admin token (201)', function () {
    $token = makeAccessToken($this->privPem, $this->kid);

    $resp = $this->postJson('/api/admin/shows', [
        'title' => 'My Podcast',
        'description' => 'Descrição',
        'cover_url' => 'https://exemplo.com/capa.jpg',
    ], [
        'Authorization' => 'Bearer '.$token,
        'Content-Type' => 'application/json',
    ])->assertCreated();

    $resp->assertJsonStructure(['data' => ['id', 'title', 'slug', 'description', 'image_url', 'created_at', 'updated_at']]);

    expect(Show::query()->count())->toBe(1);
    expect(Show::first()->title)->toBe('My Podcast');
});

it('fails 401 when aud is wrong', function () {
    $token = makeAccessToken($this->privPem, $this->kid, [
        'aud' => ['other-service'],
    ]);

    $this->postJson('/api/admin/shows', [
        'title' => 'Outro',
    ], [
        'Authorization' => 'Bearer '.$token,
    ])->assertStatus(401)->assertJson(['message' => 'Invalid token']);
});

it('fails 403 when scope is missing', function () {
    $token = makeAccessToken($this->privPem, $this->kid, [
        'scope' => ['user'],
    ]);

    $this->postJson('/api/admin/shows', [
        'title' => 'No permission',
    ], [
        'Authorization' => 'Bearer '.$token,
    ])->assertStatus(403)->assertJson(['message' => 'insufficient_scope']);
});

it('fails 422 on validation error', function () {
    $token = makeAccessToken($this->privPem, $this->kid);

    $this->postJson('/api/admin/shows', [
        'cover_url' => 'https://exemplo.com/capa.jpg',
    ], [
        'Authorization' => 'Bearer '.$token,
    ])->assertStatus(422);
});
