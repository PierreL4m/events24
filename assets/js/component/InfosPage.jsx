import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {GlobalContext} from "./Context/GlobalContext.jsx";
import ReactHtmlParser from 'react-html-parser';
import {SectionsContext} from "./Context/SectionsContext.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function InfosPage (props) {
    const value = useContext(GlobalContext);
    const sections = useContext(SectionsContext);
    const DATE_OPTIONS = { weekday: 'long', month: 'long', day: 'numeric' };
    const hour = {hour:'numeric'};
    const styles = {
        backEvent: {
            backgroundColor: value.place.colors[0].code,
        },
        whiteText: {
            color: value.place.colors[2].code,
        },
        coloredText: {
            color: value.place.colors[1].code,
        },
        hr: {
            borderColor: value.place.colors[0].code,
        },
        place: {
            fontWeight: '700',
            fontSize: '1.5em',
            textTransform: 'uppercase',
            color: '#534b48',
        },
        adress: {
            color:'#938f8c',
        },
        hours: {
            margin: '10px 0 20px',
            fontWeight: '700',
            color:'#938f8c',
        },
        download: {
            marginBottom: '10px',
            fontWeight: '700',
            color:'#938f8c',
        },
    };
    return (
        <React.Fragment>
            {sections &&
            sections.map(
                section => {
                    if (section.sectionType.slug === "infos") {
                        return (
                            <React.Fragment key={section.sectionType.slug}>
                                <div className={"infosContainer row"}>
                                    <div className={"infoAdresse offersHeader"}>
                                        <div className="offersHeader">
                                            <h2>Informations pratiques</h2>
                                            <hr style={styles.hr} />
                                        </div>
                                        <p style={styles.whiteText, styles.place}>{value.place.name}</p>
                                        <p style={styles.whiteText, styles.adress}>{value.place.address}<br/>{value.place.cp} {value.place.city}</p>
                                        {value.childEvents.length > 0 ?
                                            <p style={styles.coloredText, styles.hours}>
                                                Le {moment(value.date).format('dddd DD')} et {moment(value.childEvents[0].date).format('dddd DD MMMM')}   <br/>
                                                De {moment(value.date, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')} h à {moment(value.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h{moment(value.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm') !== "00" && moment(value.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm')}
                                            </p>
                                            :
                                            <p style={styles.coloredText, styles.hours}>
                                                Le {moment(value.date).format('dddd DD MMMM')} <br/>
                                                De {moment(value.date, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')} h à {moment(value.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('HH')}h{moment(value.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm') !== "00" && moment(value.closingAt, 'yyyy-MM-dd’T’hh:mm:ss.SSSZ').format('mm')}
                                            </p>
                                        }
                                        {section.description &&
                                            <>
                                                <div className="offersHeader">
                                                    <h2>Informations supplémentaires</h2>
                                                    <hr style={styles.hr} />
                                                </div>
                                                <div style={styles.whiteText, styles.adress}>
                                                    {ReactHtmlParser(section.description)}
                                                </div>
                                            </>
                                        }
                                        <a style={styles.download} href={"/kit-presse/"+value.slug}><i className="fa-solid fa-download"></i> Télécharger le kit presse</a>
                                    </div>
                                    <div className={"infoMap"}>
                                        {/*<iframe width="100%" height="400" frameBorder="0" scrolling="no" marginHeight="0" marginWidth="0"*/}
                                        {/*src={"https://www.openstreetmap.org/export/embed.html?bbox="+value.place.longitude+"%2C"+value.place.latitude+"%2C"+value.place.longitude+"%2C"+value.place.latitude+"&amp;layer=mapnik&amp;marker="+value.place.latitude+"%2C"+value.place.longitude}></iframe>*/}
                                        {/*<br/><small><a*/}
                                        {/*href="https://www.openstreetmap.org/?mlat=47.75095&amp;mlon=-3.36896#map=19/47.75095/-3.36896">Afficher*/}
                                        {/*une carte plus grande</a></small>*/}
                                        <iframe id={"map"} frameBorder="0" scrolling="no" marginHeight="0" marginWidth="0"
                                                src={"https://www.openstreetmap.org/export/embed.html?bbox="+value.place.longitude+"%2C"+value.place.latitude+"%2C"+value.place.longitude+"%2C"+value.place.latitude+"&amp;layer=mapnik&amp;marker="+value.place.latitude+"%2C"+value.place.longitude}></iframe>
                                        <br/><small><a
                                        target={"_blank"}
                                        href={"https://www.openstreetmap.org/?mlat="+value.place.latitude+"&amp;mlon="+value.place.longitude+"#map=18/"+value.place.latitude+"/"+value.place.longitude}>Afficher
                                        une carte plus grande</a></small>
                                    </div>
                                </div>
                            </React.Fragment>
                        )
                    }
                }
            )
            }
        </React.Fragment>
    );
}