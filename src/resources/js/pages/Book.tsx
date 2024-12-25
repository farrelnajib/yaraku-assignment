import {Col, Container, Row} from "react-bootstrap";
import React from "react";
import Layout from "../layout/Layout";

export default function Book() {
    return (
        <Layout title="Default">
            <Container>
                <Row>
                    <Col>
                        <p className="h1">Book page</p>
                    </Col>
                </Row>
            </Container>
        </Layout>
    )
}
