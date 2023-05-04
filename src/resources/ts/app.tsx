import React from "react";
import {createRoot} from "react-dom/client";

const App = () => {
    return <h1> Hello React !</h1>
}

const root = createRoot(
    document.getElementById('app') as HTMLElement
)
root.render(<App/>)
