import React, {ChangeEvent, useCallback, useEffect, useState} from "react";
import {JSX} from "react";
import {Button, Card, Col, Form, Row} from "react-bootstrap";
import {faDownload} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {useFormTable} from "../contexts/FormTableContext";

export default function Export(): JSX.Element {
    const {
        handleExport,
    } = useFormTable();

    const [type, setExportType] = useState<'csv' | 'xml'>('csv');
    const [fields, setFields] = useState<string[]>([]);

    const handleExportTypeChange = useCallback((e: ChangeEvent<HTMLSelectElement>) => {
        setExportType(e.target.value == "csv" ? "csv" : "xml");
    }, []);

    const handleExportFieldsChange = useCallback((field: string) => {
        setFields((prev: string[]) => {
            // If field is already selected, remove it
            if (prev.includes(field)) {
                return prev.filter((f: string) => f !== field)
            }
            // If field is not selected, add it
            return [...prev, field]
        })
    }, [fields]);

    return (
        <Card className="h-100">
            <Card.Body>
                <Row>
                    <Form.Group className="mb-3" as={Col} controlId="type">
                        <Form.Label>Export Type</Form.Label>
                        <Form.Select onChange={handleExportTypeChange}>
                            <option value="csv">CSV</option>
                            <option value="xml">XML</option>
                        </Form.Select>
                    </Form.Group>
                </Row>
                <Row>
                    <Form.Group className="mb-3" as={Col}>
                        <Form.Label>Fields</Form.Label>
                        <Form.Check
                            type="checkbox"
                            label="Title"
                            id="checkbox-title"
                            checked={fields.includes("title")}
                            onChange={() => handleExportFieldsChange("title")}
                        />
                        <Form.Check
                            type="checkbox"
                            label="Author"
                            id="checkbox-author"
                            checked={fields.includes("author")}
                            onChange={() => handleExportFieldsChange("author")}
                        />
                    </Form.Group>
                </Row>
                <Row>
                    <Form.Group as={Col}>
                        <Button onClick={() => handleExport({type, fields})}><FontAwesomeIcon icon={faDownload} /> Export</Button>
                    </Form.Group>
                </Row>
            </Card.Body>
        </Card>
    )
}
