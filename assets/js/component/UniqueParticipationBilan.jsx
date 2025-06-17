import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route} from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx";
import AuthService from "./Services/auth.service.js";
import axios from "axios";
import UseModal from "./Hooks/UseModal.jsx";
import ModalPicturesBilan from "./ModalPicturesBilan.jsx";

export default function UniqueParticiationBilan (props) {
    const value = useContext(GlobalContext);
    // FIXME unused ? const [participation, setparticipation] = useState(null);
    // FIXME unused ? const [loading, setLoading] = useState(true);
    // FIXME unused ? const [error, setError] = useState(null);
    // FIXME unused ? const [popJob, setPopJob] = useState(null);
    const [participation, setParticipation] = useState(props.participation);
    const [nbJobs, setNbJobs] = useState(null);
    const [loading, setLoading] = useState(true);
    const styles = {
        backButton: {
            backgroundColor: value.place.colors[0].code,
        },
        infosExposants: {
            textTransform:'uppercase',
            fontSize:'11px',
            color:'grey',
            marginBottom:'10px',
        }
    };

    return (
        <React.Fragment>
            {participation &&
                <>
                    <button className={"uniqueParticiationBilan"} value={participation.id} onClick={toggleLoginForm}>
                        <div className={"fakeBoxOrga"}>
                            <div className={"fakeBoxParticipation"}>
                                <div className={"cardCarousselParticipation"}>
                                    <li style={participation.premium == true ? {
                                        borderColor: value.place.colors[0].code,
                                        borderStyle: 'solid',
                                        borderWidth: '2px'
                                    } : {borderColor: 'lightgray', borderStyle: 'solid', borderWidth: '1px'}}
                                        className="slide p-2">
                                        <p className={"organizationName"}>{participation.companyName}</p>
                                        {participation.logo &&
                                            <div className={"logoBox"}>
                                                <img src={"/uploads/" + participation.logo.path} alt=""/>
                                            </div>
                                        }
                                        <p style={styles.infosExposants} className={"infosExposants"}><img
                                            className={"pictoExpo"} src={"/images/dot.svg"}/>{participation.city}</p>
                                        {participation.sector !== null ?
                                            <p style={styles.infosExposants} className={"infosExposants"}><img
                                                className={"pictoExpo"}
                                                src={"/images/heart.svg"}/>{participation.sector.name}</p>
                                            :
                                            <p style={styles.infosExposants} className={"infosExposants"}><img
                                                className={"pictoExpo"} src={"/images/heart.svg"}/>Divers</p>
                                        }
                                        <div style={styles.backButton} className={"buttonExposantOffer"}
                                             to={{pathname: "/Exposant/" + participation.id}}>
                                            Voir les photos
                                        </div>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </button>
                    <ModalPicturesBilan isShowing={isLoginFormShowed} hide={toggleLoginForm} title="Les Photos" pictures={participation.bilanPictures}/>
                </>
            }
        </React.Fragment>
    );
}