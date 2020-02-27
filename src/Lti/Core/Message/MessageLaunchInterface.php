<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace App\Lti\Core\Message;

use App\Lti\Core\Deployment\DeploymentInterface;
use Lcobucci\JWT\Token;

interface MessageLaunchInterface
{
    // JWT headers
    public const HEADER_ALG = 'alg';
    public const HEADER_KID = 'kid';

    // JWT claims
    public const CLAIM_ISS = 'iss';
    public const CLAIM_SUB = 'sub';
    public const CLAIM_AUD = 'aud';
    public const CLAIM_EXP = 'exp';
    public const CLAIM_IAT = 'iat';
    public const CLAIM_NONCE = 'nonce';

    // User claims
    public const CLAIM_USER_NAME = 'name';
    public const CLAIM_USER_EMAIL = 'email';
    public const CLAIM_USER_GIVEN_NAME = 'given_name';
    public const CLAIM_USER_FAMILY_NAME = 'family_name';
    public const CLAIM_USER_MIDDLE_NAME = 'middle_name';
    public const CLAIM_USER_LOCALE = 'locale';
    public const CLAIM_USER_PICTURE = 'picture';

    // LTI claims
    public const CLAIM_LTI_MESSAGE_TYPE = 'https://purl.imsglobal.org/spec/lti/claim/message_type';
    public const CLAIM_LTI_VERSION = 'https://purl.imsglobal.org/spec/lti/claim/version';
    public const CLAIM_LTI_DEPLOYMENT_ID = 'https://purl.imsglobal.org/spec/lti/claim/deployment_id';
    public const CLAIM_LTI_TARGET_LINK_URI = 'https://purl.imsglobal.org/spec/lti/claim/target_link_uri';
    public const CLAIM_LTI_ROLES = 'https://purl.imsglobal.org/spec/lti/claim/roles';
    public const CLAIM_LTI_RESOURCE_LINK = 'https://purl.imsglobal.org/spec/lti/claim/resource_link';
    public const CLAIM_LTI_CONTEXT = 'https://purl.imsglobal.org/spec/lti/claim/context';
    public const CLAIM_LTI_TOOL_PLATFORM = 'https://purl.imsglobal.org/spec/lti/claim/tool_platform';
    public const CLAIM_LTI_ROLE_SCOPE_MENTOR = 'https://purl.imsglobal.org/spec/lti/claim/role_scope_mentor';
    public const CLAIM_LTI_LAUNCH_PRESENTATION = 'https://purl.imsglobal.org/spec/lti/claim/launch_presentation';
    public const CLAIM_LTI_CUSTOM = 'https://purl.imsglobal.org/spec/lti/claim/custom';

    public function getId(): string;

    public function getDeployment(): DeploymentInterface;

    public function getToken(): Token;

    public function getState(): ?Token;

    public function getMessageType(): string;

    public function getVersion(): string;

    public function getDeploymentId(): string;

    public function getTargetLinkUri(): string;

    public function getRoles(): array;

    public function getResourceLink(): ?array;

    public function isAnonymous(): bool;
}