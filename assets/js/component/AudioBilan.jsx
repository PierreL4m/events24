import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {BrowserRouter as Router, Link, Route} from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import DigitalBoost from "./DigitalBoost.jsx";
import LoaderElement from "./Loader.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import UniqueParticipationBilan from "./UniqueParticipationBilan";
import CarouselExposantBilan from "./CarouselExposantBilan";

export default function AudioBilan (props) {
    const styles = {
        title: {
            textAlign: 'center',
            marginTop:'50px',
            marginBottom:'50px',
        },
        name: {
            fontSize: "14px",
            textTransform: "uppercase",
            letterSpacing: "1px",
            color: "#595959",
            fontWeight:"bold"
        }
    };
    return (
        <React.Fragment>
            <div className={"col-12"}>
                <div className={"row"}>
                    <div className={"col-12 offersHeader"}>
                        <h2 style={styles.title}>Audio(s)</h2>
                    </div>
                </div>
                <div className={"audioContainer"}>
                    {props.interview.map((audio, index) => {
                        return (
                            <div style={{width: "100%"}}>
                                <div className={"col-12 offersHeader"}>
                                    <p style={styles.name}>{audio.name}</p>
                                </div>
                                <audio style={{width: "100%"}} controls src={"/uploads/bilan/" + audio.path}></audio>
                            </div>
                        )
                    })}
                </div>
            </div>
        </React.Fragment>
    );
}