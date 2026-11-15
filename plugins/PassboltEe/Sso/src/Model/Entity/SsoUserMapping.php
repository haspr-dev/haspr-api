<?php
declare(strict_types=1);

/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 */

namespace Passbolt\Sso\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property string $id
 * @property string $user_id
 * @property string $provider
 * @property string $claim_name
 * @property string $claim_value
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class SsoUserMapping extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'id' => false,
        'user_id' => false,
        'provider' => false,
        'claim_name' => false,
        'claim_value' => false,
        'created' => false,
        'modified' => false,
    ];
}
