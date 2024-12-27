/**
 * Props for `PaginationComponent`.
 */
export interface PaginationProps {
    currentPage: number;
    totalPages: number;
    handlePageClick: (page: number) => void;
}
