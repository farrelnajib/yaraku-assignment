import React, {ChangeEvent} from "react";
import {APIErrors} from "../../helpers/types";

export interface FormData {
    id?: number;
    title: string;
    author: string;
}

/**
 * Props for FormTableContext
 */
export interface FormTableContextType {
    // Form related context
    formData: FormData;
    submitFormError: APIErrors | null;
    handleInputChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    handleSubmitForm: (e: React.FormEvent<HTMLFormElement>) => void;
    handleResetForm: () => void;
    handleEditData: (idx: number) => void;

    // Search related context
    searchTerm: string;
    handleSearchInput: (e: ChangeEvent<HTMLInputElement>) => void;

    // Sort related context
    sortField: keyof FormData | null;
    sortDirection: 'asc' | 'desc';
    handleSort: (column: keyof FormData) => void;

    // Pagination related context
    currentPage: number;
    perPage: number;
    handlePerPageChange: (perPage: number) => void;
    handlePageClick: (page: number) => void;

    // Table response related context
    tableData: FormData[];
    totalPages: number;
    isLoading: boolean;
    fetchTableDataError: string | null;
}
