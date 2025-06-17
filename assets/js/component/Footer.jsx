import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {GlobalContext} from "./Context/GlobalContext.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function Footer () {
    return (
        <React.Fragment>
            <div className={"linkFooterCarousel"}>
                <a href="https://fr-fr.facebook.com/L4M.fr/" target="_blank"><img className={"socialLogo"} src="/images/facebook.svg" alt="facebook"/></a>
                <a href="https://fr.linkedin.com/company/l4m-fr-looking-for-mission" target="_blank"><img className={"socialLogo"} src="/images/linkdine.svg" alt="linkedin"/></a>
                <a href="https://www.instagram.com/l4m.fr/?hl=fr" target="_blank"><img className={"socialLogo"} src="/images/insta.svg" alt="instagram"/></a>
                <a href="https://twitter.com/l4mfr" target="_blank"><i className="fa-brands fa-x-twitter" style={{color: "#393938", fontSize:"25px"}}></i></a>
                <a href="https://www.l4m.fr/" target="_blank"><img className={"orgaLogo"} src="/images/L4M_orga.svg" alt="l4m"/></a>
            </div>
        </React.Fragment>
    );
}