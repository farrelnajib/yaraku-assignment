import {ChangeEvent} from "react";

export interface FormValues {
    title: string;
    author: string;
}

export interface FormData {
    id?: number;
    title: string;
    author: string;
}

/**
 * Props for FormTableContext
 */
export interface FormTableContextType {
    formData: FormData | null;
    isLoading: boolean;
    error: string | null;

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
}
