import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import ReactGA from "react-ga4";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import Concept from "./Concept.jsx";
import KeyNumbers from "./KeyNumbers.jsx";
import Sectors from "./Sectors.jsx";
import CarouselExposantBilan from "./CarouselExposantBilan.jsx";
import Infos from "./Infos.jsx";
import LoaderElement from "./Loader.jsx";
import AuthService from "./Services/auth.service.js";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import CarouselExposant from "./CarouselExposant";

export default function HomeBilan (props) {
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/", title: "Accueil" });
    }, []);
    const value = useContext(GlobalContext);
    const partners = value.partners;
    const sections = useContext(SectionsContext);
    const DATE_OPTIONS = { weekday: 'long', month: 'long', day: 'numeric' };
    const DATE_OPTIONS_SUB = { month: 'numeric', day: 'numeric'};
    const styles = {
        title: {
            textAlign: 'center',
            marginTop:'50px',
            marginBottom:'50px',
        }
    };
    return (
        <React.Fragment>
            <div className={"col-12"}>
                <div className={"row"}>
                    <div className={"col-12 offersHeader"}>
                        <h2 style={styles.title}>Les photos de l'événement</h2>
                    </div>
                </div>
                {sections &&
                    sections.map(
                        section => {
                            if (section.sectionType.slug === "company") {
                                return (<CarouselExposantBilan key={section.sectionType.slug}/>)
                            }
                        }
                    )
                }
            </div>
        </React.Fragment>
    );
}