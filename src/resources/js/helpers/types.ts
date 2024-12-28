export interface ValidationErrors {
    [key: string]: string[];
}

export interface APIErrors {
    message: string
    errors?: ValidationErrors
}
