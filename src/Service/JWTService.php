<?php

namespace App\Service;

class JWTService
{
    // génération du Json Web Token
    // validity contien la durée du token en secondes

    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        // TODO: ajouter gestion d'erreur
        // check du temps valide
        // dans le if pour ne pas écraser les données de validité
        if ($validity > 0) {
            $now = new \DateTimeImmutable();
            $expiration = $now->getTimestamp() + $validity;

            // iat= issued at
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $expiration;
        }

        // On encore en base64 après avoir encoder en json
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // on "nettoie" les valeurs encodées (retrait des +,/,=)
        $base64Header = str_replace(['+', '/',  '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/',  '='], ['-', '_', ''], $base64Payload);

        // Generation de la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header.'.'.$base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/',  '='], ['-', '_', ''], $base64Signature);

        // On crée le token
        $jwt = $base64Header.'.'.$base64Payload.'.'.$base64Signature;

        return $jwt;
    }

    // On vérifie la validité du token (correctement formé)
    public function isValid(string $token): bool
    {
        // regex pour vérifier la forme
        return 1 === preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        );
    }

    // Récupération du Header
    public function getHeader(string $token): array
    {
        // on démonte le token
        $array = explode('.', $token);

        // On décode le Header
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // Récupération du payload pour vérifier l'espiration
    public function getPayload(string $token): array
    {
        // on démonte le token
        $array = explode('.', $token);

        // On décode le payload
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    // Vérification de la date d'espiration
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new \DateTimeImmutable();

        // Si true alors le token a expiré
        return $payload['exp'] < $now->getTimestamp();
    }

    // Vérification de la signature du Token
    public function checkSignature(string $token, string $secret)
    {
        // Récup. du header et payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // Génération d'un nouveau token pour comparer les signatures
        // le 0 permet de ne pas avoir à regénérer des dâtes d'exp
        $verifToken = $this->generate($header, $payload, $secret, 0);

        // renvois true si vérif. acceptée
        return $token === $verifToken;
    }
}
