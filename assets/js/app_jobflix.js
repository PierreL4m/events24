import React, {Component, useContext, useEffect} from "react";
import {BrowserRouter as Router, NavLink, Route, Switch, hashHistory} from "react-router-dom";
import "core-js/stable";
import "regenerator-runtime/runtime";
import {render, unmountComponentAtNode} from 'react-dom'
import "../css/app.css";
import EventContext, {Context, GlobalContext} from "./component/Context/EventContext.jsx"
import { LoadingProvider } from "./component/Context/LoadingContext";
import EventsElement from "./component/Events.jsx";
import LayoutElement from "./component/Layout.jsx";
import LayoutBilanElement from "./component/LayoutBilan.jsx";
import UserContainer from "./component/UserContainer.jsx";
import Login24 from "./component/Login24.jsx";
import ProfilCandidat from "./component/ProfilCandidat.jsx";
import { Helmet } from "react-helmet";
import CheckSubscribe from "./component/checkSubscribe";
import CookieConsent from "react-cookie-consent";
import HubCandidats from "./component/hubCandidats";
import CandidatForm from "./component/candidatForm";
import CandidatEvent from "./component/candidatEvent";
import HomeConcept from "./component/HomeConcept";
class IndexElement extends Component {
    render(){
        return(
            <LoadingProvider>
                <EventContext>
                    <Router>
                        <div className={"container-fluid layout"}>
                            <Switch>
                                <Route exact path={"/"}>
                                    <>
                                        <Helmet>
                                            <html lang="en" />
                                            <title>24H pour l'emploi et la formation, Jobfest, Recrutement Expert, Recrut Comedy Club, venez à la rencontre des recruteurs lors des événements L4M dans les régions Hauts-de-France, Normandie, Bretagne, Pays de la Loire et Centre Val de Loire </title>
                                            <meta name="description" content="L4M organise depuis 2004 des salons de l’emploi et de la formation en France. Retrouvez nous dans les régions Hauts-de-France, Normandie, Bretagne, Pays de la Loire et Centre Val de Loire et participez à l'un de nos 25 événements lors d'un 24h pour l'emploi et la formation, Jobfest, Recrutement Expert ou Recrut Comedy Club." />
                                        </Helmet>
                                        <UserContainer />
                                        <EventsElement />
                                    </>
                                </Route>
                                <Route exact path={"/accueil/concept"}>
                                    <>
                                        <Helmet>
                                            <html lang="en" />
                                            <title>24H pour l'emploi et la formation, Jobfest, Recrutement Expert, Recrut Comedy Club, venez à la rencontre des recruteurs lors des événements L4M dans les régions Hauts-de-France, Normandie, Bretagne, Pays de la Loire et Centre Val de Loire </title>
                                            <meta name="description" content="L4M organise depuis 2004 des salons de l’emploi et de la formation en France. Retrouvez nous dans les régions Hauts-de-France, Normandie, Bretagne, Pays de la Loire et Centre Val de Loire et participez à l'un de nos 25 événements lors d'un 24h pour l'emploi et la formation, Jobfest, Recrutement Expert ou Recrut Comedy Club." />
                                        </Helmet>
                                        <UserContainer />
                                        <HomeConcept />
                                    </>
                                </Route>
                                <Route exact path={"/:slug"}>
                                    {/*<UserContainer />*/}
                                    <LayoutElement />
                                </Route>
                                <Route exact path={"/bilan/:slug"}>
                                    {/*<UserContainer />*/}
                                    <LayoutBilanElement />
                                </Route>
                                <Route exact path={"/auth/login"}>
                                    <Login24 />
                                </Route>
                                <Route exact path={"/candidat/home"}>
                                    <HubCandidats />
                                </Route>
                                <Route exact path={"/candidat/profile"}>
                                    <ProfilCandidat />
                                </Route>
                                <Route exact path={"/candidat/events"}>
                                    <CandidatEvent />
                                </Route>
                            </Switch>
                            <CookieConsent
                                enableDeclineButton
                                location="bottom"
                                declineButtonText="Refuser"
                                buttonText="Accepter"
                                cookieName="myAwesomeCookieName2"
                                style={{ background: "#2B373B" }}
                                buttonStyle={{ color: "#4e503b", fontSize: "13px" }}
                                expires={150}
                            >
                                Ce site web utilise des cookies afin d'améliorer l'experience de l'utilisateur aucune de ces données n'est utilisée à des fins commerciales. {" "}
                            </CookieConsent>
                        </div>
                    </Router>
                </EventContext>
            </LoadingProvider>
        );
    }
}

class Index extends HTMLElement{

    connectedCallback(){
        render(<IndexElement/>, this)
    }

    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}
customElements.define('home-overlay', Index)