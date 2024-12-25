import React, {JSX, useEffect} from "react";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min";

interface ILayoutProps {
    children: JSX.Element | JSX.Element[]
    title: string
}

const Layout: React.FC<ILayoutProps> = ({ children, title }): JSX.Element | JSX.Element[] => {
    useEffect(() => {
        document.title = title;
    })

    return children;
}

export default Layout;
