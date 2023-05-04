import React, {useState} from "react";
import axios, {AxiosError} from "axios";
import {Simulate} from "react-dom/test-utils";
import error = Simulate.error;

interface FormState {
    name: string;
}


const Form = () => {

    const [formState, setFormState] = useState<FormState>({
        name: ''
    });

    const handleNameChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setFormState({...formState, name: e.target.value})
    };

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        axios.post('api/', formState)

            .then((response) => {
                console.log(response)
            })

            .catch((error: AxiosError) => {
                console.log(error);
            });
    };

    return (
        <form onSubmit={handleSubmit}>
            <div>
                <label htmlFor={"name"}>Name: </label>
                <input type={"text"} id={"name"} value={formState.name}
                       onChange={handleNameChange}/>
            </div>
            <button type={"submit"}>submit
            </button>
        </form>
    );
};

export default Form;
