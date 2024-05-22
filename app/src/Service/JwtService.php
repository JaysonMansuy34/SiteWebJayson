<?php

namespace App\Service;

use DateTimeImmutable;

class JwtService
{
    // gerer un token

    public function gerenate(
        array $header,
        array $payload,
        string $secret,
        int $validity = 10800
    ): string {

        if ($validity > 0) {
            $now = new \DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload["iat"] = $now->getTimestamp();
            $payload["exp"] = $exp;
        }

        // on encode tout en base 64 
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // On nettoie les valueurs encodéées (retrait des + / =) evite les bugs
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // On genere un secret

        $secret = base64_decode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64signature = base64_encode($signature);

        $base64signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64signature);

        // on crée le token

        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64signature;

        return $jwt;
    }

    // Verifier si le token est valide (former)
    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
    }

    //recupére si il a payload
    public function getPayload(string $token): array
    {
        //on demonte le token
        $array = explode('.', $token);

        // on decode le payload
        $payload = json_decode(base64_decode($array[1]), true);
        return $payload;
    }

    //recupére si il a header
    public function getHeader(string $token): array
    {
        //on demonte le token
        $array = explode('.', $token);

        // on decode le header
        $header = json_decode(base64_decode($array[0]), true);
        return $header;
    }

    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // on verifie la signature des tokens
    public function check(string $token, string $secret)
    {
        // on recupére le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);


        //regéneré un token  à partir du header et du payload pour vérif
        $verifToken = $this->gerenate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}
