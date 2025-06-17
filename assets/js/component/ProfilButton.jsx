import React, {useState, useEffect, Component, useContext} from "react";
import UseModal from "./Hooks/UseModal.jsx";
import Modal from "./Modal.jsx";

export default function LoginButton() {
    const { isShowing: isLoginFormShowed, toggle: toggleLoginForm } = UseModal();
    const {
        isShowing: isRegistrationFormShowed,
        toggle: toggleRegistrationForm
    } = UseModal();
    return (
        <>
            <button id="loginButton" onClick={toggleLoginForm}>
                Login <i className="fas fa-user"></i>
            </button>
            <Modal isShowing={isLoginFormShowed} hide={toggleLoginForm} title="Login" />
        </>
    );
}