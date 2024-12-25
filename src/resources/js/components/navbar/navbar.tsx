import React from "react";
import {Container, Navbar} from "react-bootstrap";

export default function NavBar() {
    return (
        <Navbar expand="lg" className="bg-body-tertiary mb-3">
            <Container>
                <Navbar.Brand href="/">Book DB</Navbar.Brand>
            </Container>
        </Navbar>
    )
}
