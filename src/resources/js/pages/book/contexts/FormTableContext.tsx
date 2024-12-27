import React, {ChangeEvent, createContext, JSX, useContext, useEffect, useMemo, useState} from "react";
import {FormTableContextType, FormData} from "../types";
import axios from "axios";
import debounce from "lodash.debounce";

/**
 * FormTableContext provides state and methods for managing table and form interactions.
 */
const FormTableContext = createContext<FormTableContextType | undefined>(undefined);

export const useFormTable: () => FormTableContextType = (): FormTableContextType => {
    const context = useContext(FormTableContext);
    if (!context) {
        throw new Error('useFormTable must be used within a FormTableProvider');
    }
    return context;
}

/**
 * Fetches table data from the API based on the given parameters.
 *
 * @param {Object} params - The parameters for the API call.
 * @param {string} params.searchTerm - The search term for filtering data.
 * @param {keyof FormData | null} params.sortField - The field to sort by.
 * @param {'asc' | 'desc'} params.sortDirection - The sorting direction.
 * @param {number} params.currentPage - The current page number.
 * @param {number} params.perPage - The number of items per page.
 * @returns {Promise<{ data: FormData[]; lastPage: number }>} The fetched table data and total pages.
 */
const fetchTableData = async ({ searchTerm, sortField, sortDirection, currentPage, perPage }: {
    searchTerm: string;
    sortField: keyof FormData | null;
    sortDirection: 'asc' | 'desc';
    currentPage: number;
    perPage: number;
}): Promise<{ data: FormData[]; lastPage: number; }> => {
    const response = await axios.get("/api/books", {
        params: {
            search_text: searchTerm,
            sort_field: sortField,
            sort_direction: sortDirection,
            page: currentPage,
            per_page: perPage,
        }
    });

    return response.data;
};

/**
 * Context provider from FormTableContext.
 *
 * @param {React.PropsWithChildren} children - The children components.
 * @returns {JSX.Element} The context provider.
 */
export const FormTableProvider: React.FC<React.PropsWithChildren<{}>> = ({ children }: React.PropsWithChildren): JSX.Element => {
    const [formData] = useState<FormData | null>(null);
    const [error, setError] = useState<string | null>(null);

    // Sort related states
    const [sortField, setSortField] = useState<keyof FormData | null>(null);
    const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('asc');

    // Table related states
    const [searchTerm, setSearchTerm] = useState<string>('');
    const [debouncedSearchTerm, setDebouncedSearchTerm] = useState<string>('');
    const [perPage, setPerPage] = useState(15);
    const [currentPage, setCurrentPage] = useState(1);
    const [tableData, setTableData] = useState<FormData[]>([]);
    const [totalPages, setTotalPages] = useState(0);
    const [isLoading, setIsLoading] = useState(false);

    const handleSort = (column: keyof FormData) => {
        setSortDirection((sortField == null) ? "asc" : (sortDirection == "asc" ? "desc" : "asc"));
        setSortField(column);
    };

    const handlePageClick = (page: number) => {
        if (page !== currentPage) {
            setCurrentPage(page);
        }
    }

    const handlePerPageChange = (perPage: number) => {
        setPerPage(perPage);
        setCurrentPage(1);
    }

    const handleSearchInput = (e: ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setSearchTerm(value);
        debouncedSearch(value);
    };

    const debouncedSearch = useMemo(
        () =>
            debounce((value: string) => {
                setDebouncedSearchTerm(value);
                setCurrentPage(1);
            }, 300),
        []
    );

    useEffect(() => {
        setIsLoading(true);
        setError(null);
        fetchTableData({
            searchTerm: debouncedSearchTerm,
            sortField,
            sortDirection,
            currentPage,
            perPage,
        }).then(data => {
            setTableData(data.data);
            setTotalPages(data.lastPage);
        }).catch(err => {
            console.log(err);
            setError("Failed to load table data. Please try again.");
        }).finally(() => {
            setIsLoading(false);

        })
    }, [perPage, debouncedSearchTerm, currentPage, sortField, sortDirection]);

    return (
        <FormTableContext.Provider value={{
            formData,
            isLoading,
            error,

            // Search related context
            searchTerm,
            handleSearchInput,

            // Sort related context
            sortField,
            sortDirection,
            handleSort,

            // Pagination related context
            currentPage,
            perPage,
            handlePerPageChange,
            handlePageClick,

            // Table response related context
            tableData,
            totalPages,
        }}>
            {children}
        </FormTableContext.Provider>
    )
}
