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

export default function BannerBilan (props) {
    const DATE_OPTIONS = { weekday: 'long', month: 'short', day: 'numeric', year: 'long' };
    const DATE_OPTIONS_SUB = { month: 'numeric', day: 'numeric'};
    const hour = {hour12: false, hour: "2-digit", hourCycle: 'h23'};
    const data = props.event;
    const styles = {
        backEvent: {
            backgroundColor: data.place.colors[0].code,
            backgroundImage:"url(/uploads/banners/"+data.bannerName+")",
            backgroundRepeat:'no-repeat',
            filter:'grayscale(0)',
            width:'100%',
            marginLeft:'-1px',
            height:'fit-content'
        },
        whiteText: {
            color: 'white',
            letterSpacing:"1px"
        },
        coloredText: {
            color: data.place.colors[1].code,
        }
    };
    const [eventDate, setEventDate] = useState(moment(new Date(data.date)).format("YYYY/MM/DD"));
    return (
        <React.Fragment>
            <div id={"anchorView"} style={styles.backEvent} className={"col-12 child gallery-cell"}>
                <p style={{color:"white",textTransform:"uppercase"}} className={"lieu"}>Retour sur l'événement</p>
                <nav>
                    <Link className={"eventButton"} to={{pathname:"/", idEventProps:{id:data.id}}}>
                        <p className={"textEventTop"}>
                            <span className={"ville"} style={styles.coloredText}>{data.place.city}</span>
                            <br/>
                            <span className={"lieu"} style={styles.whiteText}>{data.place.name}</span>
                        </p>
                        { data.childEvents.length > 0 ?
                            <p className={"textEventBot"}>
                                <span className={"date"} style={styles.coloredText}>Du {moment(data.date).format('dddd DD MMMM YYYY')} au {moment(data.childEvents[0].date).format('dddd DD MMMM YYYY')}</span>
                                <br/>
                            </p>
                            :
                            <p className={"textEventBot"}>
                                <span className={"date"} style={styles.coloredText}>{moment(data.date).format('dddd DD MMMM YYYY')}</span>
                            </p>
                        }
                    </Link>
                </nav>
            </div>
        </React.Fragment>
    );
}