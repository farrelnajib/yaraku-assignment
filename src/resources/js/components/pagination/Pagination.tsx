import React, {JSX, useMemo} from "react";
import {Pagination} from "react-bootstrap";
import {PaginationProps} from "./types";

/**
 * calculatePageRange - Calculate smallest and largest page number
 * @param {number} currentPage - The current active page
 * @param {number} totalPages - The total number of pages.
 * @param {number} [range=2] - The number of pages to display before and after the current page.
 * @returns {{smallest: number, largest: number}} - An object containing the smallest and largest page numbers to display.
 *
 * @example
 * // For currentPage = 3, totalPages = 10, range = 2
 * calculatePageRange(3, 10, 2);
 * // Returns { smallest: 1, largest: 5 }
 *
 * @example
 * // For currentPage = 1, totalPages = 5, range = 2
 * calculatePageRange(1, 5, 2);
 * // Returns { smallest: 1, largest: 5 }
 *
 * @example
 * // For currentPage = 10, totalPages = 10, range = 2
 * calculatePageRange(10, 10, 2);
 * // Returns { smallest: 5, largest: 10 }
 *
 */
const calculatePageRange = (currentPage: number, totalPages: number, range: number = 2): {smallest: number, largest: number} => {
    let smallest = currentPage - range;
    let largest = currentPage + range;

    if (smallest < 1) {
        largest += (1 - smallest);
        smallest = 1;
    }

    if (largest > totalPages) {
        smallest = Math.max(1, smallest - (largest - totalPages));
        largest = totalPages;
    }

    return { smallest, largest };
}

/**
 * A pagination component for navigating pages.
 *
 * @param {Object} props - The props for the component
 * @param {number} props.currentPage - The current active page
 * @param {number} props.totalPages - The total number of pages.
 * @param {(page: number) => void} props.handlePageClick - The action when a page is clicked.
 * @returns {JSX.Element} - A pagination component.
 */
const PaginationComponent: React.FC<PaginationProps> = ({currentPage, totalPages, handlePageClick}: {
    currentPage: number;
    totalPages: number;
    handlePageClick: (page: number) => void;
}): JSX.Element => {

    const {smallest, largest} = calculatePageRange(currentPage, totalPages);

    const pages = useMemo(() => {
        const pageArray: JSX.Element[] = [];
        for (let idx = smallest; idx <= largest; idx++) {
            pageArray.push(
                <Pagination.Item key={idx} active={idx == currentPage} onClick={() => handlePageClick(idx)}>
                    {idx}
                </Pagination.Item>
            )
        }
        return pageArray;
    }, [currentPage, totalPages, smallest, largest]);

    return (
        <Pagination>
            <Pagination.First onClick={() => handlePageClick(1)} disabled={currentPage == 1} />
            <Pagination.Prev onClick={() => handlePageClick(currentPage - 1)} disabled={currentPage == 1} />
            {pages}
            <Pagination.Next onClick={() => handlePageClick(currentPage + 1)} disabled={currentPage == totalPages} />
            <Pagination.Last onClick={() => handlePageClick(totalPages)} disabled={currentPage == totalPages} />
        </Pagination>
    )
};

export default PaginationComponent;
