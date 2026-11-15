<?php
declare(strict_types=1);

namespace Passbolt\Sso\Service\SsoUserMappings;

use Cake\ORM\Locator\LocatorAwareTrait;
use Passbolt\Sso\Model\Entity\SsoUserMapping;

class SsoUserMappingsGetService
{
    use LocatorAwareTrait;

    /**
     * @param string $provider provider
     * @param string $claimName claim name
     * @param string $claimValue claim value
     * @return \Passbolt\Sso\Model\Entity\SsoUserMapping|null
     */
    public function findByClaim(string $provider, string $claimName, string $claimValue): ?SsoUserMapping
    {
        /** @var \Passbolt\Sso\Model\Table\SsoUserMappingsTable $table */
        $table = $this->fetchTable('Passbolt/Sso.SsoUserMappings');

        /** @var \Passbolt\Sso\Model\Entity\SsoUserMapping|null $mapping */
        $mapping = $table->find()
            ->where([
                'provider' => $provider,
                'claim_name' => $claimName,
                'claim_value' => $claimValue,
            ])
            ->first();

        return $mapping;
    }

    /**
     * @param string $userId user id
     * @param string $provider provider
     * @param string $claimName claim name
     * @return \Passbolt\Sso\Model\Entity\SsoUserMapping|null
     */
    public function findForUser(string $userId, string $provider, string $claimName): ?SsoUserMapping
    {
        /** @var \Passbolt\Sso\Model\Table\SsoUserMappingsTable $table */
        $table = $this->fetchTable('Passbolt/Sso.SsoUserMappings');

        /** @var \Passbolt\Sso\Model\Entity\SsoUserMapping|null $mapping */
        $mapping = $table->find()
            ->where([
                'user_id' => $userId,
                'provider' => $provider,
                'claim_name' => $claimName,
            ])
            ->first();

        return $mapping;
    }
}
