<?php
declare(strict_types=1);

namespace Passbolt\Sso\Service\SsoUserMappings;

use App\Service\Users\UserGetService;
use App\Utility\UserAccessControl;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ConflictException;
use Passbolt\Sso\Model\Entity\SsoSetting;
use Throwable;

class SsoUserMappingsSyncService
{
    /**
     * @param \App\Utility\UserAccessControl $uac access control
     * @param array $data request payload
     * @return array<string, mixed>
     */
    public function sync(UserAccessControl $uac, array $data): array
    {
        if (!$uac->isAdmin()) {
            throw new BadRequestException(__('Only administrators can manage SSO user mappings.'));
        }

        $provider = $data['provider'] ?? null;
        $claimName = $data['claim_name'] ?? null;
        $mappings = $data['mappings'] ?? null;

        if (!is_string($provider) || !in_array($provider, SsoSetting::ALLOWED_PROVIDERS, true)) {
            throw new BadRequestException(__('The provider is not supported.'));
        }
        if (!is_string($claimName) || $claimName === '') {
            throw new BadRequestException(__('The claim name should not be empty.'));
        }
        if (!is_array($mappings) || $mappings === []) {
            throw new BadRequestException(__('The mappings should not be empty.'));
        }

        $result = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'errors' => []];
        $setService = new SsoUserMappingsSetService();
        $userGetService = new UserGetService();

        foreach ($mappings as $mapping) {
            $userId = $mapping['user_id'] ?? null;
            $claimValue = $mapping['claim_value'] ?? null;

            if (!is_string($userId) || $userId === '' || !is_string($claimValue) || $claimValue === '') {
                $result['errors'][] = [
                    'user_id' => $userId,
                    'claim_value' => $claimValue,
                    'message' => __('The mapping payload is invalid.'),
                ];
                continue;
            }

            try {
                $userGetService->getActiveNotDeletedNotDisabledOrFail($userId);
                $status = $setService->createOrUpdateForUser($userId, $provider, $claimName, $claimValue);
                $result[$status]++;
            } catch (ConflictException | BadRequestException $e) {
                $result['errors'][] = [
                    'user_id' => $userId,
                    'claim_value' => $claimValue,
                    'message' => $e->getMessage(),
                ];
            } catch (Throwable $e) {
                $result['errors'][] = [
                    'user_id' => $userId,
                    'claim_value' => $claimValue,
                    'message' => __('The mapping could not be synchronized.'),
                ];
            }
        }

        return $result;
    }
}
