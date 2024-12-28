import axios, {AxiosResponse} from "axios";
import {FormData} from "../types";

/**
 * Get list books from the API based on the given parameters.
 *
 * @param {Object} params - The parameters for the API call.
 * @param {string} params.searchTerm - The search term for filtering data.
 * @param {keyof FormData | null} params.sortField - The field to sort by.
 * @param {'asc' | 'desc'} params.sortDirection - The sorting direction.
 * @param {number} params.currentPage - The current page number.
 * @param {number} params.perPage - The number of items per page.
 * @returns {Promise<{ data: FormData[]; lastPage: number }>} The fetched table data and total pages.
 */
export const getListBooks = async ({ searchTerm, sortField, sortDirection, currentPage, perPage }: {
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
 * Send POST book data to `/api/books/` for create or
 * PUT to `/api/books/{id}` for update
 *
 * @param {FormData} data
 * @returns {Promise<AxiosResponse>} - The raw API response.
 *
 */
export const upsertBook = async (data: FormData): Promise<AxiosResponse> => {
    let url = "/api/books";
    let method = "post";
    if (data.id) {
        url += `/${data.id}`;
        method = "put";
    }

    return await axios.request({method, url, data});
}

/**
 * Send DELETE request to `/api/books/{id}`
 *
 * @param {number} id - ID of the book that want to be deleted.
 * @returns {Promise<AxiosResponse>} The raw API response.
 *
 */
export const deleteBook = async (id: number): Promise<AxiosResponse> => {
    return await axios.delete(`/api/books/${id}`);
}
