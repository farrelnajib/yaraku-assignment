import React, {ChangeEvent, createContext, JSX, useCallback, useContext, useEffect, useMemo, useState} from "react";
import {FormTableContextType, FormData} from "../types";
import debounce from "lodash.debounce";
import {handleAPIError} from "../../../helpers/errors";
import {APIErrors} from "../../../helpers/types";
import Swal from "sweetalert2";
import withReactContent from "sweetalert2-react-content";
import {deleteBook, getListBooks, upsertBook} from "./services";

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
 * Context provider from FormTableContext.
 *
 * @param {React.PropsWithChildren} children - The children components.
 * @returns {JSX.Element} The context provider.
 */
export const FormTableProvider: React.FC<React.PropsWithChildren<{}>> = ({ children }: React.PropsWithChildren): JSX.Element => {
    // Form related states
    const [formData, setFormData] = useState<FormData>({title: "", author: ""});
    const [submitFormError, setSubmitFormError] = useState<APIErrors | null>(null);

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
    const [fetchTableDataError, setFetchTableDataError] = useState<string | null>(null);

    const handleInputChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData({...formData, [name]: value});

        if (submitFormError) {
            setSubmitFormError(null);
        }
    }, [formData]);

    const handleResetForm = useCallback(() => {
        setFormData({title: "", author: ""});
        setSubmitFormError(null);
    }, []);

    const handleSubmitForm = useCallback((e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        upsertBook(formData)
            .then(() => {
                handleResetForm();
                fetchTableData();
            })
            .catch(error => {
                const apiErr = handleAPIError(error);
                setSubmitFormError(apiErr);
            })
    }, [formData]);

    const handleEditData = useCallback((idx: number) => {
        const dataToEdit = tableData[idx];
        setFormData(dataToEdit);
    }, [tableData])

    const handleSort = useCallback((column: keyof FormData) => {
        setSortDirection((sortField == null) ? "asc" : (sortDirection == "asc" ? "desc" : "asc"));
        setSortField(column);
    }, [sortField, sortDirection]);

    const handlePageClick = useCallback((page: number) => {
        if (page !== currentPage) {
            setCurrentPage(page);
        }
    }, [currentPage])

    const handlePerPageChange = useCallback((perPage: number) => {
        setPerPage(perPage);
        setCurrentPage(1);
    }, [perPage])

    const handleSearchInput = useCallback((e: ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setSearchTerm(value);
        debouncedSearch(value);
    }, [searchTerm]);

    const debouncedSearch = useMemo(
        () =>
            debounce((value: string) => {
                setDebouncedSearchTerm(value);
                setCurrentPage(1);
            }, 300),
        []
    );

    const fetchTableData = useCallback(() => {
        setIsLoading(true);
        setFetchTableDataError(null);
        getListBooks({
            searchTerm: debouncedSearchTerm,
            sortField,
            sortDirection,
            currentPage,
            perPage,
        }).then(data => {
            setTableData(data.data);
            setTotalPages(data.lastPage);
        }).catch(error => {
            const apiError = handleAPIError(error);
            setFetchTableDataError(apiError.message);
        }).finally(() => {
            setIsLoading(false);
        })
    }, [perPage, debouncedSearchTerm, currentPage, sortField, sortDirection]);

    const handleDeleteData = useCallback((id: number) => {
        const swal = withReactContent(Swal);

        swal.fire({
            title: "Do you want to delete this book?",
            text: "This action is irreversible. The book will be deleted permanently and cannot be recovered",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete",
            confirmButtonColor: "red"
        })
            .then((result) => {
                if (result.isConfirmed) {
                    deleteBook(id)
                        .then(() => {
                            fetchTableData();
                        })
                        .catch(err => {
                            const apiErr = handleAPIError(err);
                            setFetchTableDataError(apiErr.message);
                        });
                }
            });
    }, [fetchTableData])

    useEffect(() => {
        fetchTableData();
    }, [fetchTableData, perPage, debouncedSearchTerm, currentPage, sortField, sortDirection]);

    return (
        <FormTableContext.Provider value={{
            // Form related context
            formData,
            submitFormError,
            handleInputChange,
            handleSubmitForm,
            handleResetForm,
            handleEditData,
            handleDeleteData,

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
            fetchTableDataError,
            isLoading,
        }}>
            {children}
        </FormTableContext.Provider>
    )
}