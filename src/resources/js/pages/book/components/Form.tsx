import {Button, Card, Col, Form, Row} from "react-bootstrap";
import React from "react";
import * as formik from "formik";
import * as yup from "yup";
import {FormikHelpers, FormikValues} from "formik";
import {FormValues} from "../types";

export default function FormComponent() {
    const {Formik} = formik;
    const schema = yup.object().shape({
        title: yup.string().required(),
        author: yup.string().required()
    });

    const handleSubmit = (
        values: FormikValues,
        {setSubmitting}: FormikHelpers<FormValues>,
    ) => {
        console.log(values)
        setSubmitting(false)
    }

    return (
        <Row className="mb-3">
            <Col>
                <Card>
                    <Card.Body>
                        <Formik
                            onSubmit={handleSubmit}
                            validateOnBlur={true}
                            initialValues={{
                                title: '',
                                author: '',
                            }}
                            validationSchema={schema}
                        >
                            {({handleSubmit, handleChange, values, touched, errors}) => (
                                <Form noValidate onSubmit={handleSubmit}>
                                    <Row>
                                        <Form.Group className="mb-3" controlId="title" as={Col}>
                                            <Form.Label>Title</Form.Label>
                                            <Form.Control
                                                type="text"
                                                placeholder="Lorem ipsum dolor sit amet"
                                                name="title"
                                                value={values.title}
                                                onChange={handleChange}
                                                isValid={touched.title && !errors.title}
                                            />
                                            <Form.Control.Feedback type="invalid">{errors.title}</Form.Control.Feedback>
                                        </Form.Group>
                                    </Row>
                                    <Row>
                                        <Form.Group className="mb-3" controlId="author" as={Col}>
                                            <Form.Label>Author</Form.Label>
                                            <Form.Control
                                                type="text"
                                                placeholder="John Doe"
                                                name="author"
                                                value={values.author}
                                                onChange={handleChange}
                                                isValid={touched.author && !errors.author}
                                            />
                                            <Form.Control.Feedback type="invalid">{errors.author}</Form.Control.Feedback>
                                        </Form.Group>
                                    </Row>
                                    <Row>
                                        <Col>
                                            <Button type="submit">Submit</Button>
                                        </Col>
                                    </Row>
                                </Form>
                            )}
                        </Formik>
                    </Card.Body>
                </Card>
            </Col>
        </Row>
    )
}
