import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {GlobalContext} from "./Context/GlobalContext.jsx";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import Modal from "./Modal";
import UseModal from "./Hooks/UseModal";
import ModalSubscribe from "./ModalSubscribe";

export default function SubscribeLayout (props) {
    const value = useContext(GlobalContext);
    const sections = useContext(GlobalContext);
    const [isActive, setActive] = useState('unexpand');

    const { isShowing: isLoginFormShowed, toggle: toggleLoginForm } = UseModal();
    const {
        isShowing: isRegistrationFormShowed,
        toggle: toggleRegistrationForm
    } = UseModal();
    const styles = {
        backEvent: {
            backgroundColor: value.place.colors[0].code,
        }
    }
    return (
        <React.Fragment>
                <div onClick={toggleLoginForm} id={"subscribeBaseText"}>
                    <div className={"flex-item"}></div>
                    {props.isPublic === true
                        ?
                        value.type.full_name === "Recrutement Experts" ?
                            <div className={"flex-item"}>Inscription</div>
                            :
                            <div className={"flex-item"}>Inscription coupe-file</div>
                        :
                        <div className={"flex-item"}>Préinscription</div>
                    }
                    <div className={"flex-item"}></div>
                </div>
                <div>
                    <ModalSubscribe isPublic={props.isPublic} isShowing={isLoginFormShowed} hide={toggleLoginForm} title="Login" />
                </div>
        </React.Fragment>
    );
}