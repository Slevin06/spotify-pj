import React, {Fragment} from "react";
import {createRoot} from "react-dom/client";
import SpotifyAuthButton from "./SpotifyAuthButton";

const App = () => {
    return (
        <Fragment>
            <h1>DJ tamayu</h1>
            <SpotifyAuthButton/>
        </Fragment>
    );
};

const root = createRoot(
    document.getElementById('app') as HTMLElement
);
root.render(<App/>);
