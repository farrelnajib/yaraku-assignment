import {Button, Col, Row, Table} from "react-bootstrap";
import React, {JSX, useEffect, useMemo, useState} from "react";
import {useFormTable, FormData} from "../contexts/FormTableContext";
import axios from "axios";
import PaginationComponent from "../../../components/pagination/Pagination";

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
            current_page: currentPage,
            per_page: perPage,
        }
    });

    return response.data;
};

/**
 * TableHeader - Handles sort in table header
 *
 * @param {Object} props - The props for the component.
 * @param {(column: keyof FormData) => void} props.onSort - The action when header column clicked.
 * @param {keyof FormData | null} props.sortField - Name of the field.
 * @param {'asc' | 'desc'} props.sortDirection - Direction of sort, can be 'asc' or 'desc'.
 * @returns {JSX.Element} A table header component.
 */
const TableHeader = ({ onSort, sortField, sortDirection }: {
    onSort: (column: keyof FormData) => void,
    sortField: keyof FormData | null,
    sortDirection: 'asc' | 'desc'
}): JSX.Element => (
    <thead>
        <tr>
            <th>#</th>
            <th onClick={() => onSort('title')} style={{cursor: "pointer"}}>
                Title {sortField == 'title' ? (sortDirection === 'asc' ? ' ▲' : ' ▼') : ''}
            </th>
            <th onClick={() => onSort('author')} style={{cursor: "pointer"}}>
                Author {sortField == 'author' ? (sortDirection === 'asc' ? ' ▲' : ' ▼') : ''}
            </th>
            <th>Action</th>
        </tr>
    </thead>
);

/**
 * TableRow - A single row in the table displaying item details.
 *
 * @param {Object} props - The props for the component.
 * @param {number} props.index - The index of the row.
 * @param {FormData} props.item - The data for the row.
 * @param {number} props.currentPage - The current page of the table.
 * @param {number} props.perPage - Number of items displayed per page.
 * @returns {JSX.Element} A table row component.
 */
const TableRow = ({ item, index, currentPage, perPage }: {
    item: FormData,
    index: number,
    currentPage: number,
    perPage: number,
}): JSX.Element => (
    <tr key={index}>
        <td>{(currentPage - 1) * perPage + index + 1}</td>
        <td>{item.title}</td>
        <td>{item.author}</td>
        <td>
            <Button variant="primary">Edit</Button>
            &nbsp;
            <Button variant="danger">Delete</Button>
        </td>
    </tr>
);

/**
 * LoadingIndicator - Loading Indicator for Table. Need to provide `colSpan` as params
 *
 * @param {number} colSpan
 * @returns {JSX.Element} A custom loading component
 */
const LoadingIndicator = ({ colSpan }: { colSpan: number }): JSX.Element => (
    <tr>
        <td colSpan={colSpan}>
            <div className="d-flex justify-content-center">
                <div className="spinner-border" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        </td>
    </tr>
);

/**
 * TableComponent - Table displaying books data with pagination and sorting capabilities.
 *
 * @returns JSX.Element The books table component
 */
export default function TableComponent() {
    const {
        searchTerm,
        perPage,
        currentPage,
        setCurrentPage,
    } = useFormTable();

    const [tableData, setTableData] = useState<FormData[]>([]);
    const [totalPages, setTotalPages] = useState(0);
    const [isLoading, setIsLoading] = useState(false);

    const [sortField, setSortField] = useState<keyof FormData | null>(null);
    const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('asc');

    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        setIsLoading(true);
        setError(null);
        fetchTableData({
            searchTerm,
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
    }, [perPage, searchTerm, currentPage, sortField, sortDirection]);

    const handleSort = (column: keyof FormData) => {
        setSortDirection((sortField == null) ? "asc" : (sortDirection == "asc" ? "desc" : "asc"));
        setSortField(column);
    };

    const dataComponent = useMemo(() => (
        tableData.map((item, index) => (
            <TableRow
                key={index}
                item={item}
                index={index}
                currentPage={currentPage}
                perPage={perPage}
            />
        ))
    ), [tableData, currentPage, perPage])

    return (
        <Row>
            <Col>
                {error && <div className="alert alert-danger">{error}</div>}
                <Row>
                    <Col>
                        <Table striped>
                            <TableHeader onSort={handleSort} sortField={sortField} sortDirection={sortDirection} />
                            <tbody>
                            {isLoading ? <LoadingIndicator colSpan={4} /> : dataComponent}
                            </tbody>
                        </Table>
                    </Col>
                </Row>
                <Row>
                    <Col>{ isLoading ? <></> :
                        <PaginationComponent
                            currentPage={currentPage}
                            totalPages={totalPages}
                            setCurrentPage={setCurrentPage}
                        />}
                    </Col>
                </Row>
            </Col>
        </Row>
    )
}
