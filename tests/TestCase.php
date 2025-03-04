<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{

    protected function apiAs(User $user, string $method, string $uri, array $data = [])
    {
        $headers = [
            "Authentication" => "Bearer " . Auth::login($user),
            "Accept" => "application/json"
        ];

        return $this->json($method, $uri, $data, $headers);
    }
}
