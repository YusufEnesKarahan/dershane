<?php

namespace App\Domain\HQ\Services\IAM\SSO;

use App\Models\User;

interface IdentityProviderInterface
{
    /**
     * Redirect the user to the IdP
     */
    public function redirect();

    /**
     * Handle the callback from the IdP and return the user details
     */
    public function handleCallback(): array;

    /**
     * Sync IdP user details with local user
     */
    public function syncUser(array $idpUser): User;
}
