<?php
declare(strict_types = 1);

namespace App\Enum;

enum UploadFileError: int
{
    /**
     * The token is missing or expired.
     * Get a new token from the POST /login endpoint.
     */
    case InvalidToken = 0;

    /**
     * The user doesn’t own the analysis.
     * Alternatively, the user is not allowed to use the API.
     * This might happen if the user has become an employee.
     * Employees aren't allowed to use the API.
     */
    case ApiAccessForbidden = 1;

    /**
     * The analysis mentioned by ID doesn’t exist or was stopped.
     */
    case NoSuchAnalysis = 2;

    /** A file with that name has already been uploaded. */
    case AlreadyUploaded = 3;

    /** The uploaded file is too large. */
    case TooLarge = 4;

    /**
     * The file is not a FASTQ or FAST5 file,
     * or the type does not match the type specified when creating the pipeline.
     */
    case FormatMismatch = 5;

    /**
     * The pipeline is not fully started and waiting for it timed out.
     * Alternatively, the upload was rejected because the server doesn’t have
     * any resources available to analyse a file.
     */
    case RetryLater = 6;

    /**
     * The API request failed.
     */
    case UnknownError = 7;
}
