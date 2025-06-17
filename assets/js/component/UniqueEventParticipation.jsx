import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {BrowserRouter as Router, Link, Route} from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import LoaderElement from "./Loader.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import UseModal from "./Hooks/UseModal";
import ModalQrCode from "./ModalQrCode";

export default function UniqueEventParticipation (props) {
    const DATE_OPTIONS = { weekday: 'long', month: 'long', day: 'numeric' };
    const DATE_OPTIONS_SUB = { month: 'numeric', day: 'numeric'};
    const hour = {hourCycle: 'h23', hour: "2-digit"};
    const data = props.event;
    const currentDate = new Date();
    const styles = {
        backEvent: {
            backgroundColor: data.place.colors[0].code,
            backgroundImage:"url(/uploads/banners/"+data.bannerName+")",
            backgroundRepeat:'no-repeat',
            backgroundSize:"contain",
            height:'160px'
        },
        whiteText: {
            color: 'white',
            textAlign:'center'
        },
        coloredText: {
            color: data.place.colors[1].code,
            textAlign:'center'
        },
        backButton:{
            backgroundColor: data.place.colors[0].code,
            color: data.place.colors[1].code,
            border: "none",
            borderRadius: "0",
            textTransform: "uppercase",
            letterSpacing: "2px",
            fontWeight:"bold"

        }
    };

    const { isShowing: isLoginFormShowed, toggle: toggleLoginForm } = UseModal();
    const {
        isShowing: isRegistrationFormShowed,
        toggle: toggleRegistrationForm
    } = UseModal();
    return (
        <React.Fragment>
            <div style={styles.backEvent}>
                <a className={"eventButton"} href={"/" + data.slug}>
                    <p style={{textAlign: "center"}} className={"textEventTop"}>
                        <span className={"ville"} style={styles.coloredText}>{data.place.city}</span>
                        <br/>
                        <span className={"lieu"} style={styles.whiteText}>{data.place.name}</span>
                    </p>
                    {data.childEvents.length > 0 ?
                        <p style={{textAlign: "center"}} className={"textEventBot"}>
                            <span className={"date"}
                                  style={styles.coloredText}>Du {moment(data.date).format('dddd DD MMMM')} au {moment(data.childEvents[0].date).format('dddd DD MMMM')}</span>
                            <br/>
                            <span className={"heure"}
                                  style={styles.whiteText}>{moment(data.date, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h - {moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h{moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm') !== "00" && moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm')}</span>
                        </p>
                        :
                        <p style={{textAlign: "center"}} className={"textEventBot"}>
                            <span className={"date"}
                                  style={styles.coloredText}>{moment(data.date).format('dddd DD MMMM')}</span>
                            <br/>
                            <span className={"heure"}
                                  style={styles.whiteText}>{moment(data.date, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h - {moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h{moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm') !== "00" && moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm')}</span>
                        </p>
                    }
                </a>
            </div>
            <div className={"gestionMyEvents"}>
                <a className="gestionMyEventsButton" href={"/" + data.slug} target={"_blank"}>
                    Voir le site de l'événement
                </a>
                <a className="gestionMyEventsButton" href={"/" + data.slug + '#/Exposants'} target={"_blank"}>
                    Voir les exposants
                </a>
                <a className="gestionMyEventsButton" href={"/" + data.slug + '#/Offres'} target={"_blank"}>
                    Voir les offres
                </a>
                <button style={styles.backButton}
                        className={"gestionMyEventsButton"} onClick={toggleLoginForm}>
                    Voir mon QR code
                </button>
            </div>

            <ModalQrCode qrCode={props.user.candidateParticipations[0].qrCode} isShowing={isLoginFormShowed}
                         hide={toggleLoginForm} title="Login"/>
        </React.Fragment>
    );
}
