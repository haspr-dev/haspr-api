<?php
declare(strict_types=1);

namespace Passbolt\Sso\Service\SsoUserMappings;

use App\Error\Exception\ValidationException;
use App\Utility\UuidFactory;
use Cake\Http\Exception\ConflictException;
use Cake\ORM\Locator\LocatorAwareTrait;

class SsoUserMappingsSetService
{
    use LocatorAwareTrait;

    /**
     * @param string $userId user id
     * @param string $provider provider
     * @param string $claimName claim name
     * @param string $claimValue claim value
     * @return string created|updated|unchanged
     */
    public function createOrUpdateForUser(
        string $userId,
        string $provider,
        string $claimName,
        string $claimValue
    ): string {
        $getService = new SsoUserMappingsGetService();
        $byClaim = $getService->findByClaim($provider, $claimName, $claimValue);
        if ($byClaim && $byClaim->user_id !== $userId) {
            /** @var \Passbolt\Sso\Model\Table\SsoUserMappingsTable $table */
            $table = $this->fetchTable('Passbolt/Sso.SsoUserMappings');
            /** @var \App\Model\Entity\User|null $owner */
            $owner = $table->getAssociation('Users')->find()
                ->where(['Users.id' => $byClaim->user_id])
                ->first();
            if ($owner === null || !$owner->isDeleted()) {
                throw new ConflictException(__('The claim value is already assigned to another user.'));
            }
            if (!$table->delete($byClaim)) {
                throw new ValidationException(
                    __('It is not possible to delete the stale SSO user mapping.'),
                    $byClaim,
                    $table
                );
            }
        }

        /** @var \Passbolt\Sso\Model\Table\SsoUserMappingsTable $table */
        $table = $this->fetchTable('Passbolt/Sso.SsoUserMappings');
        $existing = $getService->findForUser($userId, $provider, $claimName);
        if ($existing) {
            if ($existing->claim_value === $claimValue) {
                return 'unchanged';
            }

            $existing = $table->patchEntity($existing, ['claim_value' => $claimValue], [
                'accessibleFields' => ['claim_value' => true],
            ]);
            if (!$table->save($existing)) {
                throw new ValidationException(
                    __('It is not possible to save the SSO user mapping.'),
                    $existing,
                    $table
                );
            }

            return 'updated';
        }

        $entity = $table->newEntity([
            'id' => UuidFactory::uuid(),
            'user_id' => $userId,
            'provider' => $provider,
            'claim_name' => $claimName,
            'claim_value' => $claimValue,
        ], [
            'accessibleFields' => [
                'id' => true,
                'user_id' => true,
                'provider' => true,
                'claim_name' => true,
                'claim_value' => true,
            ],
        ]);

        if (!$table->save($entity)) {
            throw new ValidationException(__('It is not possible to save the SSO user mapping.'), $entity, $table);
        }

        return 'created';
    }
}
