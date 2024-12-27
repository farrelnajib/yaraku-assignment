import React, {createContext, JSX, useContext, useState} from "react";

export interface FormData {
    id?: number;
    title: string;
    author: string;
}

/**
 * Props for FormTableContext
 */
interface FormTableContextType {
    formData: FormData | null;
    searchTerm: string;
    setSearchTerm: (term: string) => void;
    perPage: number;
    setPerPage: (perPage: number) => void;
    currentPage: number;
    setCurrentPage: (page: number) => void;
}

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
    const [formData] = useState<FormData | null>(null)
    const [searchTerm, setSearchTerm] = useState<string>('');
    const [perPage, setPerPage] = useState(15);
    const [currentPage, setCurrentPage] = useState(1);

    return (
        <FormTableContext.Provider value={{
            formData,
            searchTerm,
            setSearchTerm,
            perPage,
            setPerPage,
            currentPage,
            setCurrentPage,
        }}>
            {children}
        </FormTableContext.Provider>
    )
}
