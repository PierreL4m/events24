import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import ReactHtmlParser from 'react-html-parser';
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function Concept (props) {
    const value = useContext(GlobalContext);
    const styles = {
        backSection: {
            backgroundPositionX: 'right',
            backgroundSize: 'contain',
            backgroundRepeat: 'no-repeat',
        },
    };
    return (
        <React.Fragment>
                    <div id={"conceptContainer"} style={styles.backSection}>
                        <div id={"textConcept"}>
                            {ReactHtmlParser(props.concept.description)}
                        </div>
                    </div>

        </React.Fragment>
    );
}