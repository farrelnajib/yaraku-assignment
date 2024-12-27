import {useFormTable} from "../contexts/FormTableContext";
import { JSX} from "react";
import {Button, Col, Form, InputGroup, Row} from "react-bootstrap";
import React from "react";

/**
 * A `<Row>` displaying items per page selection and search input
 *
 * @returns {JSX.Element} The search and pagination element for books table
 */
export default function SearchComponent(): JSX.Element {
    const {
        perPage,
        searchTerm,
        handlePerPageChange,
        handleSearchInput,
    } = useFormTable();

    return (
        <Row className="justify-content-between mb-2">
            <Col lg={3} md={6} xs={12}>
                <Form.Group as={Row}>
                    <Col xl={4} xs={6}>
                        <Form.Select
                            value={perPage}
                            onChange={(e) => handlePerPageChange(Number(e.target.value))}
                        >
                            <option value={3}>3</option>
                            <option value={15}>15</option>
                            <option value={50}>50</option>
                            <option value={100}>100</option>
                        </Form.Select>
                    </Col>
                    <Form.Label column xl={8} xs={6}>per page</Form.Label>
                </Form.Group>
            </Col>
            <Col lg={6} md={6} xs={12}>
                <InputGroup>
                    <InputGroup.Text id="searchInput">Search</InputGroup.Text>
                    <Form.Control
                        type="text"
                        placeholder="Search by title or author"
                        value={searchTerm}
                        onChange={handleSearchInput}
                        aria-labelledby="searchInput"
                    />
                </InputGroup>
            </Col>
            <Col lg={3} md={6} xs={12}>
                <InputGroup>
                    <Form.Select>
                        <option value={"csv"}>CSV</option>
                        <option value={"xml"}>XML</option>
                    </Form.Select>
                    <Button variant="primary">Export</Button>
                </InputGroup>
            </Col>
        </Row>
    )
}
