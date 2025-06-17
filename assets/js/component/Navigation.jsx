import {HashRouter as Router, Link, Route} from "react-router-dom";
import React, {useState, useEffect, Component, useContext} from "react"
import ParticipationsElement from "./Participations.jsx";
import HomeElement from "./Home.jsx";
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import CheckIfState from "./Hooks/CheckIfState.jsx";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import AuthService from "./Services/auth.service.js";
import DigitalBoost from "./DigitalBoost";


export default function Navigation () {
    const value = useContext(GlobalContext);
    const sections = useContext(SectionsContext);
    const [offers, setOffers] = useState(null);
    const [classState, setClassState] = useState("");
    const [user, setUser] = useState(null);
    const getOffers = async () => {
        const { data } = await AuthService
            .get('/api/event/jobs/'+value.id)
            .then(res => {
                return res;
            })
            .catch(e => {});
        setOffers(data);
    };
    const collapsedNav = async (event) => {
        event.target.addClass("collapsed")
    }
    console.log(sections);
    return(
        <>
            <nav className="navbar navbar-expand-lg navbar-light">
                <button className={"navbar-toggler"} type="button" data-toggle="collapse"
                        data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span className="navbar-toggler-icon"></span>
                </button>
                <div className="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div className="navbar-nav">
                        <ul className="navbar-nav mr-auto">
                            {sections &&
                                <>

                                    <li className="nav-item">
                                        <a className={"nav-link"} href={window.location.origin}>
                                            <img className={"homeImg"} src={"/images/home.svg"} />
                                        </a>
                                    </li>
                                    <li className="nav-item">
                                        <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/"}>
                                            Accueil
                                        </Link>
                                    </li>

                                    {
                                        sections.map((section, index) => {
                                            if(section.title == "Entreprises présentes"){
                                                if(section.onPublic == true){
                                                    return(
                                                        <>
                                                            <li className="nav-item">
                                                                <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/Exposants"}>
                                                                    Exposants
                                                                </Link>
                                                            </li>
                                                            <li className="nav-item">
                                                                <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/Offres"}>
                                                                    Offres
                                                                </Link>
                                                            </li>
                                                        </>
                                                    )
                                                }else{
                                                    return(
                                                        <React.Fragment key={index}>
                                                            <li className="nav-item">
                                                                <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/Exposants"}>
                                                                    Exposants
                                                                </Link>
                                                            </li>
                                                        </React.Fragment>
                                                    )
                                                }
                                            }
                                        })
                                    }
                                    {sections.find(section => section.sectionType.slug === "atelier-et-conferences") &&
                                        sections.find(section => section.sectionType.slug === "atelier-et-conferences").onPublic == true &&
                                        <>
                                            <li className="nav-item">
                                                <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/Conferences"}>
                                                    Atelier et Conférences
                                                </Link>
                                            </li>
                                        </>
                                    }
                                    <li className="nav-item">
                                        <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/Infos"}>
                                            Infos Pratiques
                                        </Link>
                                    </li>
                                    <li className="nav-item my-2 my-lg-0">
                                        <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/Contact"}>
                                            Contact
                                        </Link>
                                    </li>
                                    {
                                        sections.map((section, index) => {
                                            if(section.title == "Entreprises présentes"){
                                                if(section.onPublic == true){
                                                    return(
                                                        <>
                                                            <li className="nav-item">
                                                                <Link data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/inscription"}>
                                                                    Inscription Candidats
                                                                </Link>
                                                            </li>
                                                        </>
                                                    )
                                                }
                                            }
                                        })
                                    }
                                </>
                            }
                        </ul>
                    </div>
                </div>
            </nav>
        </>
    );
}