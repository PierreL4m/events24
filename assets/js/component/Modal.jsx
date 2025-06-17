import React, { useState } from "react";
import ReactDOM from "react-dom";
import Login from "./LoginForm.jsx"

const Modal = ({setToken, isShowing, hide }) =>
    isShowing
        ? ReactDOM.createPortal(
        <>
            <div className="modal-overlay">
                <div className="modal-wrapper">
                    <div className="modal-global">
                        <div className={"divExitButton"}>
                            <button type="button" className="modal-close-button" onClick={hide}>
                                x
                            </button>
                        </div>
                        <div className="modal-header">
                            <p>Se connecter</p>
                        </div>
                        <Login setToken={setToken}/>
                    </div>
                </div>
            </div>
        </>,
        document.body
        )
        : null;

export default Modal;