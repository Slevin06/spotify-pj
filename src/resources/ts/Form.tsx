import React from "react";
import {createRoot} from "react-dom/client";

interface FormState {
    name: string;
}

const Form = () => {
    return (
        <form>
            <div>
                <label htmlFor="name">Name:</label>
                <input type="text" id="name" value=""/>
            </div>
        </form>
    );
};

const root = createRoot(
    document.getElementById('form') as HTMLElement
)
root.render(<Form/>)
