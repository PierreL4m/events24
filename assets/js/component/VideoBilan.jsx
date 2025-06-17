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

export default function VideoBilan (props) {
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
                        <h2 style={styles.title}>Video(s)</h2>
                    </div>
                </div>
                <div className={"audioContainer"}>
                    {props.interview.map((audio, index) => {
                        return (
                            <div>
                                <div className={"col-12 offersHeader"}>
                                    <p style={styles.name}>{audio.name}</p>
                                </div>
                                <video style={{display:"block"}} width="300" height="240" controls>
                                    <source src={"/uploads/bilan/" + audio.path} type="video/mp4"/>
                                    <source src={"/uploads/bilan/" + audio.path} type="video/ogg"/>
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        )
                    })}
                </div>
            </div>
        </React.Fragment>
    );
}