<?php
declare(strict_types = 1);

namespace App\Enum;

enum GetAnalysisError
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
     * The analysis mentioned by ID doesn’t exist or was stopped.
     */
    case NoSuchAnalysis;

    /**
     * The API request failed.
     */
    case UnknownError;
}
