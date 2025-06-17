import React, { useState } from "react";
import ReactDOM from "react-dom";
import Login from "./LoginForm.jsx"
import FileBase64 from 'react-file-base64';
import CvField from "./CvField";
const ModalCv = ({setToken, isShowing, hide, user }) =>
    isShowing
        ? ReactDOM.createPortal(
            <>
                <div className="modal-overlay">
                    <div className="modal-wrapper">
                        <div style={{height:"auto"}} className="modal-global">
                            <div className={"divExitButton"}>
                                <button type="button" className="modal-close-button" onClick={hide}>
                                    x
                                </button>
                            </div>
                            <div style={{marginBottom:"50px"}} className="modal-header">
                                <p>Modifier le CV</p>
                            </div>
                            <div style={{display:"flex",justifyContent:"space-around",margin:"50px"}}>
                                <CvField user={user} />
                            </div>
                        </div>
                    </div>
                </div>
            </>,
            document.body
        )
        : null;

export default ModalCv;