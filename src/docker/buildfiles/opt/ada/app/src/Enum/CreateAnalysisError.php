<?php
declare(strict_types = 1);

namespace App\Enum;


enum CreateAnalysisError implements ApiError
{
    /**
     * The token is missing or expired.
     * Get a new token from the POST /login endpoint.
     */
    case InvalidToken;

    /**
     * The user is not allowed to use the API.
     * This might happen if the user has become an employee.
     * Employees aren't allowed to use the API.
     */
    case ApiAccessForbidden;

    /**
     * The API request failed.
     */
    case UnknownError;
}
