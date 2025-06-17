import React, { useState } from "react";
import ReactDOM from "react-dom";
import Login from "./LoginForm.jsx"

const ModalQrCode = ({setToken, isShowing, hide, qrCode }) =>
    isShowing
        ? ReactDOM.createPortal(
        <>
            <div style={{width:"100%",height:"100%"}} className="modal-overlay">
                <div className="modal-wrapper">
                    <div style={{height:"auto"}} className="modal-global">
                        <div className={"divExitButton"}>
                            <button style={{top:"4px",right:"4px"}} type="button" className="modal-close-button" onClick={hide}>
                                x
                            </button>
                        </div>
                        <img style={{width:"100%"}} src={qrCode}/>
                    </div>
                </div>
            </div>
        </>,
        document.body
        )
        : null;

export default ModalQrCode;