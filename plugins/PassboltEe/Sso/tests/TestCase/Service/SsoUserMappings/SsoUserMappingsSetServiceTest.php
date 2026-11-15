<?php
declare(strict_types=1);

namespace Passbolt\Sso\Test\TestCase\Service\SsoUserMappings;

use App\Test\Factory\UserFactory;
use Passbolt\Sso\Service\SsoUserMappings\SsoUserMappingsSetService;
use Passbolt\Sso\Test\Lib\SsoIntegrationTestCase;

class SsoUserMappingsSetServiceTest extends SsoIntegrationTestCase
{
    public function testReplacesClaimOwnedByDeletedUser(): void
    {
        $deletedUser = UserFactory::make()->deleted()->persist();
        $replacementUser = UserFactory::make()->active()->persist();
        $service = new SsoUserMappingsSetService();

        $this->assertSame('created', $service->createOrUpdateForUser(
            $deletedUser->id,
            'oauth2',
            'iserv:uuid',
            '11111111-1111-1111-1111-111111111111'
        ));
        $this->assertSame('created', $service->createOrUpdateForUser(
            $replacementUser->id,
            'oauth2',
            'iserv:uuid',
            '11111111-1111-1111-1111-111111111111'
        ));
    }
}
