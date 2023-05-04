import React from "react";
import {createRoot} from "react-dom/client";
import Form from './Form';

const App = () => {
    return (
        <>
            <h1>DJ tamayu</h1>
            <Form/>
        </>
    );
};

const root = createRoot(
    document.getElementById('app') as HTMLElement
);
root.render(<App/>);
