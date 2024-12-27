import {Button, Card, Col, Container, Form, Row, Table} from "react-bootstrap";
import React, {useState} from "react";
import Layout from "../../layout/Layout";
import Navbar from "../../components/navbar/Navbar";
import FormComponent from "./components/Form";
import TableComponent from "./components/Table";
import {FormTableProvider} from "./contexts/FormTableContext";
import SearchComponent from "./components/Search";

export default function Book() {
    return (
        <FormTableProvider>
            <Layout title="Default">
                <Navbar />
                <Container>
                    <FormComponent />
                    <SearchComponent />
                    <TableComponent />
                </Container>
            </Layout>
        </FormTableProvider>
    )
}
