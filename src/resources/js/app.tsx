import React from "react";
import ReactDOM from "react-dom/client";
import {BrowserRouter, Route, Routes} from "react-router-dom";
import Book from "./pages/book/page";

function NotFound() {
    return (
        <div>404 Not Found</div>
    )
}

function Router() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<Book />} />
                <Route path="/*" element={<NotFound />}/>
            </Routes>
        </BrowserRouter>
    )
}

ReactDOM.createRoot(document.getElementById('app')!)
    .render(<Router />);
