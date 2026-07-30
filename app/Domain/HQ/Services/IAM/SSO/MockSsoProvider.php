<?php

namespace App\Domain\HQ\Services\IAM\SSO;

use App\Models\User;
use Illuminate\Support\Str;

class MockSsoProvider implements IdentityProviderInterface
{
    public function redirect()
    {
        return redirect('/api/hq/auth/sso/callback');
    }

    public function handleCallback(): array
    {
        // Mock payload from IdP
        return [
            'email' => 'sso-user@test.com',
            'name' => 'SSO User',
            'provider_id' => 'mock-id-1234',
        ];
    }

    public function syncUser(array $idpUser): User
    {
        $user = User::firstOrCreate(
            ['email' => $idpUser['email']],
            [
                'name' => $idpUser['name'],
                'password' => bcrypt(Str::random(16)), // Unused due to SSO
            ]
        );

        return $user;
    }
}
