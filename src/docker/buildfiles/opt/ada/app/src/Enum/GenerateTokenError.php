<?php
declare(strict_types = 1);

namespace App\Enum;

enum GenerateTokenError implements ApiError
{
    /**
     * The username or password are invalid.
     */
    case InvalidUsernameOrPassword;

    /**
     * The user is not allowed to use the API.
     * This might happen if the user has become an employee.
     * Employees aren't allowed to use the API.
     */
    case ApiAccessForbidden;

    /**
     * The name is already assigned to a token from that user.
     */
    case NameAlreadyExists;

    /**
     * The API request failed.
     */
    case UnknownError;
}
