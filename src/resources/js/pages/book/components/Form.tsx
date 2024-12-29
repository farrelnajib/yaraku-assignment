import {Button, Card, Col, Form, Row, Stack} from "react-bootstrap";
import React, {JSX} from "react";
import {useFormTable} from "../contexts/FormTableContext";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faFloppyDisk} from "@fortawesome/free-solid-svg-icons";

/**
 * A form group to create or edit book
 *
 * @returns {JSX.Element} The form component
 */
export default function FormComponent(): JSX.Element {
    const {
        formData,
        handleSubmitForm,
        handleInputChange,
        handleResetForm,
        submitFormError,
    } = useFormTable();

    const isEmpty: boolean = formData.title == "" || formData.author == "";

    return (
        <Card className="h-100">
            <Card.Body>
                {submitFormError && <div className="alert alert-danger">{submitFormError.message}</div>}
                <Form noValidate onSubmit={handleSubmitForm}>
                    <Row>
                        <Form.Group className="mb-3" controlId="input-title" as={Col}>
                            <Form.Label>Title</Form.Label>
                            <Form.Control
                                type="text"
                                placeholder="Lorem ipsum dolor sit amet"
                                name="title"
                                disabled={formData.id !== undefined}
                                value={formData.title}
                                onChange={handleInputChange}
                                isInvalid={!!submitFormError?.errors?.title}
                            />
                            <Form.Control.Feedback type="invalid">{submitFormError?.errors?.title?.[0]}</Form.Control.Feedback>
                        </Form.Group>
                    </Row>
                    <Row>
                        <Form.Group className="mb-3" controlId="input-author" as={Col}>
                            <Form.Label>Author</Form.Label>
                            <Form.Control
                                type="text"
                                placeholder="John Doe"
                                name="author"
                                value={formData.author}
                                onChange={handleInputChange}
                                isInvalid={!!submitFormError?.errors?.author}
                            />
                            <Form.Control.Feedback type="invalid">{submitFormError?.errors?.author?.[0]}</Form.Control.Feedback>
                        </Form.Group>
                    </Row>
                    <Row>
                        <Col>
                            <Stack direction="horizontal" gap={2}>
                                <Button type="submit" disabled={isEmpty}><FontAwesomeIcon icon={faFloppyDisk} /> Submit</Button>
                                <Button type="reset" variant="secondary" onClick={handleResetForm}>Reset</Button>
                            </Stack>
                        </Col>
                    </Row>
                </Form>
            </Card.Body>
        </Card>
    )
}
