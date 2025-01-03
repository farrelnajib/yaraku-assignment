import {Button, Col, Row, Table} from "react-bootstrap";
import React, {JSX, useMemo} from "react";
import {useFormTable} from "../contexts/FormTableContext";
import PaginationComponent from "../../../components/pagination/Pagination";
import {FormData} from "../types";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faPenToSquare, faTrash} from "@fortawesome/free-solid-svg-icons";

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
        handleEditData,
        handleDeleteData,
        isLoading,
        fetchTableDataError,
        sortField,
        sortDirection,
        tableData,
        perPage,
        currentPage,
        totalPages,
        handlePageClick,
        handleSort,
    } = useFormTable();

    const dataComponent = useMemo(() => (
        tableData.map((item, index) => (
            <tr key={index}>
                <td>{(currentPage - 1) * perPage + index + 1}</td>
                <td>{item.title}</td>
                <td>{item.author}</td>
                <td>
                    <Button variant="primary" onClick={() => handleEditData(index)}><FontAwesomeIcon icon={faPenToSquare} /></Button>
                    &nbsp;
                    <Button variant="danger" onClick={() => handleDeleteData(item.id!)}><FontAwesomeIcon icon={faTrash} /></Button>
                </td>
            </tr>
        ))
    ), [tableData, currentPage, perPage])

    return (
        <Row>
            <Col>
                {fetchTableDataError && <div className="alert alert-danger">{fetchTableDataError}</div>}
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
                            handlePageClick={handlePageClick}
                        />}
                    </Col>
                </Row>
            </Col>
        </Row>
    )
}
