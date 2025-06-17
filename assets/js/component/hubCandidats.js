import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, Link, NavLink, Route} from "react-router-dom"
import ReactGA from "react-ga4";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import Concept from "./Concept.jsx";
import KeyNumbers from "./KeyNumbers.jsx";
import Sectors from "./Sectors.jsx";
import CarouselExposant from "./CarouselExposant.jsx";
import Infos from "./Infos.jsx";
import LoaderElement from "./Loader.jsx";
import AuthService from "./Services/auth.service.js";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import ScrollToTop from "./Services/scrollToTop";
import DigitalBoost from "./DigitalBoost";
import Navigation from "./Navigation";
import UserContainer from "./UserContainer";
import Accreditations from "./accreditations";
import CandidatForm from "./candidatForm";
import ProfilCandidat from "./ProfilCandidat";
import MyEvents from "./MyEvents";
import UpcomingEvents from "./upcomingEvents";
import Footer from "./Footer";
import CandidatEvent from "./candidatEvent";

export default function HubCandidats (props) {
    const [user, setUser] = useState(null);
    const getUser = async () => {
        const   user  = await AuthService.getUser();
        setUser(user);
    }

    useEffect(() => {
        getUser();
    }, []);
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
    console.log(value);
    return (
        <React.Fragment>
            <>
                <Router>
                    <Route exact path={"/candidat/home"}>
                        <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/candidat/profile"}>
                            Mon Profil
                        </Link>
                        <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/candidat/events"}>
                            Les Evenements
                        </Link>
                    </Route>
                    <Route exact path={"/candidat/profile"}>
                        <ProfilCandidat />
                    </Route>
                    <Route exact path={"/candidat/events"}>
                        <CandidatEvent />
                    </Route>
                </Router>
            </>
        </React.Fragment>
    );
}