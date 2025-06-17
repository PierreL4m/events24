import React, { useState } from "react";
import ReactDOM from "react-dom";
import Login from "./LoginForm.jsx"

const ModalDeleteAccount = ({setToken, isShowing, hide }) =>
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
                            <div className="modal-header">
                                <p>Supprimer le compte</p>
                            </div>
                            <p style={{textAlign:"center",fontSize:"13px",fontFamily:"Ultimate",margin:"0"}}> Cliquez sur le bouton ci-dessous pour supprimer votre compte et les données qui y sont liées.</p>
                            <p style={{textAlign:"center",fontSize:"13px",fontFamily:"Ultimate",margin:"0"}}> ATTENTION CETTE OPERATION EST IRREVERSIBLE !</p>
                            <div style={{display:"flex",justifyContent:"space-around",marginTop:"20px"}}>
                                <button className="btn btn-success"  type="button" onClick={hide}>
                                    Annuler
                                </button>
                                <form style={{marginTop:"10px"}} method="post" action="/espace-candidat/supprimer-mon-compte" id="a_confirm_form">
                                    <input type="hidden" name="_method" value="DELETE" />
                                    <input type="hidden" name="_token"
                                           value="df31fc700200bf0e32a8d74b37f40.nKjeko9kGfCOtX_nKqQqIZq-xlDjxsFGjFrbNVee4cw.8vCt9LhVaJS24kqqTol1Vqj2rmeKp6IruCCxRD75rb_UxqbFykkuvL6BGA" />
                                    <button className="btn btn-danger a_confirm is_form"
                                            data-msg="définitivement votre compte ? (Cette opération est irréversible)"
                                            id="delete_account">
                                        Valider
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </>,
            document.body
        )
        : null;

export default ModalDeleteAccount;