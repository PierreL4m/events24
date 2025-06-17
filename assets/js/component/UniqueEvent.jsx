import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {BrowserRouter as Router, Link, Route} from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import LoaderElement from "./Loader.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function UniqueEvent (props) {
    const DATE_OPTIONS = { weekday: 'long', month: 'long', day: 'numeric', year: 'long' };
    const DATE_OPTIONS_SUB = { month: 'numeric', day: 'numeric'};
    const hour = {hourCycle: 'h23', hour: "2-digit"};
    const data = props.event;
    const currentDate = new Date();
    const styles = {
        backEvent: {
            backgroundColor: data.place.colors[0].code,
            backgroundImage:"url(/uploads/banners/"+data.bannerName+")",
            backgroundRepeat:'no-repeat',
        },
        whiteText: {
            color: 'white',
        },
        coloredText: {
            color: data.place.colors[1].code,
        }
    };
    return (
        <React.Fragment>            
                <>
                    <div className={"bandeauEvent"}>{moment(data.online, 'YYYY-MM-DD’T’hh:mm:ss.SSSZ').format() < moment().format() ? "Inscriptions Ouvertes" : "Inscriptions candidat le "+moment(data.online, 'YYYY-MM-DD’T’hh:mm:ss.SSSZ').format('DD/MM')}</div>
                    <div className={"triangle"}></div>
                    <nav>
                        <Link className={"eventButton"} to={{pathname:"/"+data.slug+"", idEventProps:{id:data.id}}}>
                            <p className={"textEventTop"}>
                                <span className={"ville"} style={styles.coloredText}>{data.place.city}</span>
                                <br/>
                                <span className={"lieu"} style={styles.whiteText}>{data.place.name}</span>
                            </p>
                            { data.childEvents.length > 0 ?
                                <p className={"textEventBot"}>
                                    <span className={"date"} style={styles.coloredText}>Du {moment(data.date).format('dddd DD MMMM YYYY')} au {moment(data.childEvents[0].date).format('dddd DD MMMM')}</span>
                                    <br/>
                                    <span className={"heure"} style={styles.whiteText}>{moment(data.date, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h - {moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h{moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm') !== "00" && moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm')}</span>
                                </p>
                                :
                                <p className={"textEventBot"}>
                                    <span className={"date"} style={styles.coloredText}>{moment(data.date).format('dddd DD MMMM YYYY')}</span>
                                    <br/>
                                    <span className={"heure"} style={styles.whiteText}>{moment(data.date, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h - {moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h{moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm') !== "00" && moment(data.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm')}</span>
                                </p>
                            }
                            <button className={"mobileContent showEventButton"}>Voir</button>
                        </Link>
                    </nav>
                    <div className={"bg"} style={styles.backEvent}></div>
                </>
        </React.Fragment>
    );
}
