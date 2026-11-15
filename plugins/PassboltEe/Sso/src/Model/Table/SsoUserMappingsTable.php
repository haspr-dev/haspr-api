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

namespace Passbolt\Sso\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Passbolt\Sso\Model\Entity\SsoSetting;

class SsoUserMappingsTable extends Table
{
    /**
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('sso_user_mappings');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users');
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->uuid('user_id', __('The user id should be a valid UUID.'))
            ->requirePresence('user_id', 'create', __('A user id is required.'))
            ->notEmptyString('user_id', __('The user id should not be empty.'));

        $validator
            ->requirePresence('provider', 'create', __('A provider is required.'))
            ->notEmptyString('provider', __('The provider should not be empty.'))
            ->maxLength('provider', 64)
            ->inList('provider', SsoSetting::ALLOWED_PROVIDERS, __('The provider is not supported.'));

        $validator
            ->requirePresence('claim_name', 'create', __('A claim name is required.'))
            ->notEmptyString('claim_name', __('The claim name should not be empty.'))
            ->maxLength('claim_name', 128, __('The claim name is too large.'));

        $validator
            ->requirePresence('claim_value', 'create', __('A claim value is required.'))
            ->notEmptyString('claim_value', __('The claim value should not be empty.'))
            ->maxLength('claim_value', 255, __('The claim value is too large.'));

        return $validator;
    }

    /**
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add(
            $rules->isUnique(
                ['provider', 'claim_name', 'claim_value'],
                __('The claim value is already assigned to another user.')
            )
        );

        return $rules;
    }
}
