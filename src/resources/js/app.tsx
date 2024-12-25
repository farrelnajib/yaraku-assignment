import React, {Children, JSX, useEffect} from "react";
import ReactDOM from "react-dom/client";
import {Col, Container, Row} from "react-bootstrap";
import Layout from "./layout/Layout";
import {BrowserRouter, Route, Routes} from "react-router-dom";
import Book from "./pages/Book";



function App() {
    return (
        <Layout title="Default">
            <Container>
                <Row>
                    <Col>
                        <p className="h1">Hello World</p>
                    </Col>
                </Row>
            </Container>
        </Layout>
    )
}

function Router() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<App />} />
                <Route path="/book" element={<Book />} />
            </Routes>
        </BrowserRouter>
    )
}

ReactDOM.createRoot(document.getElementById('app')!)
    .render(<Router />);
