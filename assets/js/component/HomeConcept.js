import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
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

export default function HomeConcept (props) {
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
            {sections &&
                sections.map(
                    section => {
                        if(section.sectionType.slug === "concept"){
                            return(<Concept key={section.sectionType.slug} concept={section}/>)
                        }
                    }
                )
            }
            { value.type.shortName != "Experts" &&
                <div className={"infographie"}>
                    <div className={"infographieContainer"}>
                        <KeyNumbers />
                    </div>
                    <div className={"infographieContainer"}>
                        <Sectors />
                    </div>
                </div>
            }
            <div className={"row"}>
                <div className={"col-12 offersHeader"}>
                    <h2 style={styles.title}>Entreprises présentes</h2>
                </div>
            </div>
            <div className={"col-12"}>
                {sections &&
                    sections.map(
                        section => {
                            if(section.sectionType.slug === "company"){
                                if(section.onPublic === true){
                                    return(<CarouselExposant key={section.sectionType.slug} />)
                                }else{
                                    if(props.user !== null){
                                        if(props.user.roles[0] == "ROLE_SUPER_ADMIN"|| props.user.roles[0] == "ROLE_ADMIN"){
                                            return(<CarouselExposant key={section.sectionType.slug} />)
                                        }else{
                                            return(<p className="text-center displayAt inforamtifExposant"> Venez découvrir la liste complète des exposants participant à cet événement à partir du <strong>{moment(value.online, 'YYYY-MM-DD’T’hh:mm:ss.SSSZ').format('DD/MM')}</strong></p>)
                                        }
                                    }else{
                                        return(<p className="text-center displayAt inforamtifExposant"> Venez découvrir la liste complète des exposants participant à cet événement à partir du <strong>{moment(value.online, 'YYYY-MM-DD’T’hh:mm:ss.SSSZ').format('DD/MM')}</strong></p>)
                                    }
                                }
                            }
                        }
                    )
                }
            </div>
            {sections &&
                sections.map(
                    section => {
                        if(section.sectionType.slug === "infos"){
                            return(<Infos key={section.sectionType.slug} infos={section}/>)
                        }
                    }
                )
            }
        </React.Fragment>
    );
}