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

namespace App\Action\Platform\Security\OAuth2;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AccessTokenAction
{
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse([
            "scope" =>  "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly https://purl.imsglobal.org/spec/lti-ags/scope/score https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly",
            "access_token" => "eyJhbGciOiJSUzI1NiIsImtpZCI6IlZVNUJOYVU2a0xZVlV2aWRneTZGbUpoUC1xc2pCSldtUUIwMkNyd2MzMm8ifQ.eyJjdXN0b20iOnt9LCJzY29wZSI6Imh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpLWFncy9zY29wZS9saW5laXRlbSBodHRwczovL3B1cmwuaW1zZ2xvYmFsLm9yZy9zcGVjL2x0aS1hZ3Mvc2NvcGUvcmVzdWx0LnJlYWRvbmx5IGh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpLWFncy9zY29wZS9zY29yZSBodHRwczovL3B1cmwuaW1zZ2xvYmFsLm9yZy9zcGVjL2x0aS1ucnBzL3Njb3BlL2NvbnRleHRtZW1iZXJzaGlwLnJlYWRvbmx5IiwiaXNzIjoiaHR0cHM6Ly9sdGktcmkuaW1zZ2xvYmFsLm9yZyIsImF1ZCI6IjEyMzQ1IiwiaWF0IjoxNTgyODIxODgwLCJleHAiOjE1ODI4MjIxODAsInN1YiI6IjE0MTU5MWRiMmRiNGE0N2RlOTYyIiwibm9uY2UiOiIyZWE4NDY4ZDk2ZGVjNmNiNDRkZCIsInBsYXRmb3JtX2lkIjo3MjB9.G2zvjOalQW8wav3grE9vAJ2eU3n0RbsJyu5nFMEhXOS67ttnmwIQ-lLMEZJ4JsSJT-B_xGjKfyYM286End05-jPjEmLlRRkpfN366zhzZQ8h8uqU_ngB6dtvfQIcU6FGI_dUYE3wmbEKR1a31FbxFFMzYM7FbQTeaF60QLXa_ah8HbiFwo2Z1nkGPUYU_iKYb5dUo5MF6ECcKgyN1BpYJEWlceEKYw54DWOqnqnnfMLDovfAuPCe5pvQauadLm1OoMneGAb223QXHK0PCfcAIuiHmvhxfL13kqptk9OxZviWoWMjCFovPwb-zqJN8sEJ2nEuBwj5n1U9LCEWEnzzUA",
            "token_type" => "Bearer",
            "expires_in" => 3600
        ]);
    }
}
