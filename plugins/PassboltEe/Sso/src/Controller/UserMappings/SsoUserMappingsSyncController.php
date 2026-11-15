<?php
declare(strict_types=1);

namespace Passbolt\Sso\Controller\UserMappings;

use App\Controller\AppController;
use Cake\Http\Exception\BadRequestException;
use Passbolt\Sso\Service\SsoUserMappings\SsoUserMappingsSyncService;

class SsoUserMappingsSyncController extends AppController
{
    /**
     * @return void
     */
    public function sync(): void
    {
        $this->User->assertIsAdmin();

        $data = $this->request->getData();
        if (!is_array($data) || !count($data)) {
            throw new BadRequestException(__('The request data should not be empty.'));
        }

        $result = (new SsoUserMappingsSyncService())->sync($this->User->getAccessControl(), $data);
        $this->success(__('The SSO user mappings were synchronized.'), $result);
    }
}
