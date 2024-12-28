import {APIErrors, ValidationErrors} from "./types";
import {AxiosError} from "axios";

/**
 * Handles Axios error by converting error to AxiosError and then run `handleErrorMessage` and `handleValidationError`
 * if provided in the params.
 *
 * @param {unknown} error - From `catch`
 * @returns {APIErrors} - APIError that confirms to Laravel standard error
 */
export const handleAPIError = (error: unknown): APIErrors => {
    let message: string | undefined;
    let errors: ValidationErrors | undefined;

    if (error instanceof AxiosError && error.response) {
        const { data } = error.response;
        message = data.message;
        errors = data.errors;
    }

    return {
        message: message ?? "Unspecified error",
        errors
    }
}
