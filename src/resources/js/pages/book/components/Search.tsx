import {useFormTable} from "../contexts/FormTableContext";
import {ChangeEvent, JSX, useMemo, useState} from "react";
import debounce from "lodash.debounce";
import {Col, Form, InputGroup, Row} from "react-bootstrap";
import React from "react";

/**
 * A `<Row>` displaying items per page selection and search input
 *
 * @returns {JSX.Element} The search and pagination element for books table
 */
export default function SearchComponent(): JSX.Element {
    const [searchValue, setSearchValue] = useState<string>('');

    const {
        perPage,
        setPerPage,
        setSearchTerm,
        setCurrentPage,
    } = useFormTable();

    const handlePerPageChange = (perPage: number) => {
        setPerPage(perPage);
        setCurrentPage(1);
    }

    const onSearchInput = (e: ChangeEvent<HTMLInputElement>) => {
        setSearchValue(e.target.value);
        debouncedSearch(e.target.value);
    };

    const debouncedSearch = useMemo(
        () =>
            debounce((value: string) => {
                setSearchTerm(value);
                setCurrentPage(1);
            }, 300),
        []
    );

    return (
        <Row className="justify-content-between mb-2">
            <Col lg={4} md={6} xs={12}>
                <Form.Group as={Row}>
                    <Col className="col-4">
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
                    <Form.Label column xs={8}>entries per page</Form.Label>
                </Form.Group>
            </Col>
            <Col lg={4} md={6} xs={12}>
                <InputGroup>
                    <Form.Control
                        type="text"
                        placeholder="Search by title or author"
                        value={searchValue}
                        onChange={onSearchInput}
                    />
                </InputGroup>
            </Col>
        </Row>
    )
}
