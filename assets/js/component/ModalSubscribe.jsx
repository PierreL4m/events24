import React, { useState } from "react";
import ReactDOM from "react-dom";
import Login from "./LoginForm.jsx"
import SubscribeForm from "./SubscribeForm.jsx"
import PreRegister from "./PreRegister.jsx"

const Modal = ({setToken, isShowing,isPublic, hide }) =>
    isShowing
        ? ReactDOM.createPortal(
            <>
                <div className="modal-overlay">
                    <div className="modal-wrapper">
                        <div className="modal-global-sub">
                            <div className={"divExitButton"}>
                                <button type="button" className="modal-close-button" onClick={hide}>
                                    x
                                </button>
                            </div>
                            <div className="modal-header">
                                <p>Inscription</p>
                            </div>
                            {isPublic === true
                                ?
                                <SubscribeForm />
                                :
                                <PreRegister />
                            }
                        </div>
                    </div>
                </div>
            </>,
            document.body
        )
        : null;

export default Modal;