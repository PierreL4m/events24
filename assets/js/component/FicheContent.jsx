import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route, Switch, useLocation, Redirect, useParams,useHistory } from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx"
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function UniqueParticiation (props) {
    const value = useContext(GlobalContext);
    const styles = {
        backButton: {
            backgroundColor: value.place.colors[0].code,
        },
        infosExposants: {
            textTransform:'uppercase',
            fontSize:'11px',
            color:'grey',
            marginTop:'10px',
            marginBottom:'10px',
        }
    };
    let history = useHistory();
    return (
        <React.Fragment>
            <li className="slide">
                {props.participation.organization.id === 1243 ?
                    <p className={"organizationNameCovea"}>Covéa (MAAF, MMA et GMF)</p>
                :
                    <p className={"organizationName"}>{props.participation.companyName}</p>
                }
                <div className={"logoBox"}>
                    <img src={props.participation.logo.path} alt=""/>
                </div>
                <p style={styles.infosExposants} className={"infosExposants"}><i className="fa-solid fa-location-dot"></i>{props.participation.city}</p>
                <p style={styles.infosExposants} className={"infosExposants"}><i className="fa-regular fa-heart"></i>Secteurs d'Activitées</p>
                <p style={styles.infosExposants} className={"infosExposants"}><i className="fa-solid fa-file-signature"></i>CDI / CDD</p>
                <Link style={styles.backButton} className={"buttonExposantOffer"} to={{pathname:"/Exposant/"+props.participation.id}}>
                    Voir nos offres
                </Link>
            </li>
        </React.Fragment>
    );
}
