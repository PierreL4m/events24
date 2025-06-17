import React, {useState,useEffect, Component, useContext, useRef} from "react"
import AuthService from "./Services/auth.service.js";
import ReactGA from "react-ga4";
import 'moment/locale/fr';
import moment from "moment/moment";
import {Link} from "react-router-dom";
import axios from "axios";
import ProfilCandidat from "./ProfilCandidat";

export default function UpcomingEvents (props) {
    const [user, setUser] = useState(null);
    const getUser = async () => {
        const   user  = await AuthService.getUser();
        setUser(user);
    }

    useEffect(() => {
        getUser();
    }, []);
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/Evenements", title: "Liste_evenements" });
    }, []);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(null);
    const [checkSub, setCheckSub] = useState("No");
    const getEvents = async () => {
        const { data } = await AuthService
            .get('/api/events')
            .then(res => {
                setLoading(false);
                return res;
            });
        setData(data);
    };
    useEffect(() => {
        getEvents();
    }, []);
    const registerToEvent = (e) => {
        setCheckSub("progress");
        const event = e.target.value;
        axios.post("/api/event/"+event+"/participation",
                {
                    "event":event
                }
            )
            .then(response => {
                alert("Inscription Validée")
            }).catch(function (error) {
                alert("Inscription Validée")
                getUser()
                props.parentFunction()
                setCheckSub("No");
            });
    }
    return (
        <React.Fragment>
            {data &&
                user &&
                <>
                    <p className={"categoryProfil"}
                       style={{fontSize: "10px", marginBottom: "60px", marginTop: "40px"}}>Salons à venir</p>
                    <div style={{border: "solid #dadada 2px", padding: "10px"}}>
                        <table style={{width: "100%"}}>
                            <tbody>
                            {data.map((event, index) => {
                                    return event.parentEvent == null &&
                                        event.sections.find(section => section.title == "Inscription") &&
                                        event.sections.find(section => section.title == "Inscription").onPublic === true &&
                                        !user.candidateParticipations.find(candidateParticipation => candidateParticipation.event.id == event.id) &&
                                        <tr className={"upcomingEventTable"} style={{height: "35px"}}>
                                            <td style={{
                                                width: "25%",
                                                color: "#575756",
                                                fontWeight: "bolder",
                                                fontFamily: 'Ultimate',
                                                fontSize: "12px"
                                            }}>{event.place.city}</td>
                                            <td style={{
                                                color: "#575756",
                                                fontWeight: "bolder",
                                                fontFamily: 'Ultimate',
                                                fontSize: "12px"
                                            }}>{moment(event.date).format('dddd DD MMMM YYYY')}</td>
                                            <td><a style={{
                                                color: "white",
                                                fontFamily: 'Ultimate',
                                                fontSize: "9px",
                                                backgroundColor: "#575756",
                                                padding: "4px",
                                                width: "90%",
                                                display: "block",
                                                margin: "auto",
                                                textAlign: "center"
                                            }} href={"/" + event.slug} target={"_blank"}>voir le site</a></td>
                                            <td>
                                                {checkSub &&
                                                    <button value={event.id} onClick={registerToEvent} style={{
                                                        height: "25px",
                                                        marginTop: 0,
                                                        backgroundColor:user.candidateParticipations.length > 0 &&  user.candidateParticipations[0].event.place.colors[0].code
                                                    }} className="validerUpcomingEvents">
                                                        {checkSub == "No" ? "S'inscrire" : "Chargement..."}
                                                    </button>
                                                }
                                            </td>
                                        </tr>
                                }
                            )}
                            </tbody>
                        </table>
                    </div>
                </>
            }
            <a style={{
                fontWeight: "bolder",
                backgroundColor: "rgb(120, 120, 120)",
                borderRadius: "25px",
                width: "100%",
                marginTop: "20px",
                textAlign: "center",
                display: "block",
                color: "white",
                fontSize: "12px",
                padding: "5px"
            }} href={"/"} target={"_blank"}>
                Voir les salons à venir
            </a>
        </React.Fragment>
    );
}