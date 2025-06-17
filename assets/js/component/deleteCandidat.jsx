import React, {useState,useEffect, Component, useContext, useRef} from "react"
import AuthService from "./Services/auth.service.js";
import ReactGA from "react-ga4";
import 'moment/locale/fr';
import moment from "moment/moment";
import {Link} from "react-router-dom";
import UseModal from "./Hooks/UseModal";
import ModalDeleteAccount from "./ModalDeleteAccount";

export default function DeleteCandidat () {
    const { isShowing: isLoginFormShowed, toggle: toggleLoginForm } = UseModal();
    const {
        isShowing: isRegistrationFormShowed,
        toggle: toggleRegistrationForm
    } = UseModal();
    return (
        <React.Fragment>
        <div style={{
            width: "100%",
            maxWidth:"500px",
            marginTop: "20px",
            justifyContent: "center",
            display: "flex",
            borderRadius: "50px",
            backgroundColor: "#df0b0b"}} className={"inforamtifCandidat"}>
            <button style={{
                width: "100%",
                display: "flex",
                justifyContent: "center",
                color: "white",
                textTransform: "uppercase",
                fontWeight: "bold",
                fontSize: "17px",
            }} id="loginButton" onClick={toggleLoginForm}>
                Supprimer le compte
            </button>
        </div>
        <ModalDeleteAccount isShowing={isLoginFormShowed} hide={toggleLoginForm} title="Login" />
        </React.Fragment>
    );
}