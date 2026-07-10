<?php

namespace App\Service;

use Exception;
use Google\Api\Http;

class FirebaseTokenService
{
     /**
     * Verify Firebase ID Token using Google public endpoint
     * No service account key required!
     */
    public function verifyIdToken(string $idToken): array
    {
        $response = Http::get('https://www.googleapis.com/oauth2/v3/tokeninfo', [
            'id_token' => $idToken
        ]);

        if ($response->failed()) {
            throw new Exception('Invalid or expired Firebase token.');
        }

        $tokenData = $response->json();

        // Make sure token belongs to YOUR project
        if ($tokenData['aud'] !== env('FIREBASE_PROJECT_ID')) {
            throw new Exception('Token does not belong to this project.');
        }

        return $tokenData;
    }
}
