import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import ReactHtmlParser from 'react-html-parser';
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import ReactGA from "react-ga4";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function Conferences () {
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/conferences", title: "Conferences" });
    }, []);
    const value = useContext(GlobalContext);
    const sections = useContext(SectionsContext);
    const styles = {
        hr: {
            borderColor: value.place.colors[0].code,
        },
        selectors: {
            background: 'none',
            border: 'none',
            borderBottom:'solid 2px'+value.place.colors[0].code
        },
        none:{
            color:'#938f8c',
            marginTop:'25px',
            textAlign:'center',
        },
        title: {
            marginTop:'30px',
            marginBottom:'15px',
        },
        nbOffers:{
            textTransform: 'uppercase',
            fontWeight: 'bolder',
            color: 'grey',
        }
    };
    return (
        <React.Fragment>
            <div style={styles.title} className={"offersHeader"}>
                {sections &&
                    sections.filter(section => section.sectionType.slug.includes("atelier-et-conferences"))
                        .map((confContent) => {
                            return (
                                <>
                                    <h2>{confContent.title}</h2>
                                    <hr style={styles.hr}/>
                                    <div id={"confContainer"}>
                                        { ReactHtmlParser(confContent.description) }
                                    </div>
                                </>
                            )
                        })
                    }
            </div>
        </React.Fragment>
    );
}