import React from "react";
import {createRoot} from "react-dom/client";
import SpotifyAuth from "@/components/Auth/SpotifyAuth";
import {BrowserRouter, Route, Routes} from "react-router-dom";
import Form from "./Form";
import Layout from "@/components/Layout";

const App = () => {
  return (
      <BrowserRouter>
        <Layout>
          <Routes>
            <Route path="/" element={<SpotifyAuth/>}/>
            <Route path="/form" element={<Form/>}/>
            <Route path="*" element={<div>Page not found</div>}/>
          </Routes>
        </Layout>
      </BrowserRouter>
  );
};

const root = createRoot(
    document.getElementById('app') as HTMLElement
);
root.render(<App/>);
