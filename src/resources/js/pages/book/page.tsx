import {Button, Card, Col, Container, Form, Row, Table} from "react-bootstrap";
import React, {useState} from "react";
import Layout from "../../layout/Layout";
import NavBar from "../../components/navbar/navbar";
import * as formik from "formik";
import * as yup from "yup";
import {FormikHelpers, FormikState, FormikValues} from "formik";

interface FormValues {
    title: string;
    author: string;
}

export default function Book() {
    const [action, setAction] = useState<string>("Create");

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
        <Layout title="Default">
            <NavBar />
            <Container>
                <Row>
                    <Col>
                        <Card className="mb-2">
                            <Card.Body>
                                <Card.Title>{action}</Card.Title>
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
                <Row>
                    <Col>
                        <Table striped>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Lorem ipsum dolor sit amet</td>
                                    <td>John Doe</td>
                                    <td>
                                        <Button variant="primary">Edit</Button>
                                        &nbsp;
                                        <Button variant="danger">Delete</Button>
                                    </td>
                                </tr>
                            </tbody>
                        </Table>
                    </Col>
                </Row>
            </Container>
        </Layout>
    )
}
