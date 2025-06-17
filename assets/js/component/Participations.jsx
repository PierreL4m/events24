import {unmountComponentAtNode} from 'react-dom'
import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, Link, Route} from "react-router-dom"
import {usePaginatedFetch, useUniqueFetch} from "./Hooks/CheckIfState.jsx";
import LoaderElement from "./Loader.jsx";
import UniqueParticipation from "./UniqueParticipation.jsx";
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import AuthService from "./Services/auth.service.js";
import { Helmet } from "react-helmet";
import ReactGA from "react-ga4";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns';

export default function Participations (props) {

    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/Exposants", title: "Exposants" });
    }, []);
    const value = useContext(GlobalContext);
    const sections = useContext(SectionsContext);
    const [state, setState] = useContext(Context);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [user, setUser] = useState(null);
    const styles = {
        hr: {
            borderColor: value.place.colors[0].code,
        },
        title: {
            marginTop:'30px',
            marginBottom:'15px',
        },
        alerteExpo:{
            textAlign:'center',
            color:'rgb(83, 75, 72)',
            marginTop:'15px'
        }
    };
    const getUser = async () => {
        const { data } = await AuthService
            .get('/api/user/me')
            .then(res => {
                setLoading(false);
                return res;
            })
            .catch(e => {});
        setUser(data);
    };
    useEffect(() => {
        getUser();
    }, []);
    const getParticipations = async () => {
        const { data } = await AuthService
            .get('/api/participations/all/'+value.id)
            .then(res => {
                setLoading(false);
                return res;
            })
            .catch(e => {});
        const names = [];
        const uniqueArray = [];
        data.map((item) => {
            if (!names.includes(item.companyName)) {
                names.push(item.companyName);
                uniqueArray.push(item);
            }
        });
        setData(uniqueArray);
    };
    useEffect(() => {
        getParticipations();
    }, []);

    const displayDayParticipations = async (id) => {
        setLoading(true);

        try {
            const response = await AuthService.get('/api/participations/event/'+id);
            const result = response.data;
            setData(result);
        } catch (err) {
            setErr(err.message);
        } finally {
            setLoading(false);
        }
    };

    const displayAllParticipations = async (id) => {
        setLoading(true);

        try {
            const response = await AuthService.get('/api/participations/all/'+id);
            const result = response.data;
            const names = [];
            const uniqueArray = [];
            result.map((item) => {
                if (!names.includes(item.companyName)) {
                    names.push(item.companyName);
                    uniqueArray.push(item);
                }
            });
            setData(uniqueArray);
        } catch (err) {
            setErr(err.message);
        } finally {
            setLoading(false);
        }
    };
    return (
        <React.Fragment>
                <Helmet>
                    <html lang="en" />
                    <title>{"Exposants - "+value.type.fullName+" - "+value.place.city+" - "+moment(value.date).format('dddd DD MMMM')}</title>
                    <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+value.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                </Helmet>
            <>
                <div style={styles.title} className="offersHeader">
                    <h2>Exposants présents</h2>
                    <hr style={styles.hr} />
                </div>
                {value &&
                value.childEvents.length > 0 &&
                    sections &&
                    sections.map(section => {
                        return(
                            section.sectionType.slug === "company" &&
                                user ?
                                    user.roles[0].includes("ADMIN") &&
                                    <div className={"triExposantsBox"}>
                                        <button onClick={() => displayAllParticipations(value.id)}
                                                className={"triExposantsButton"}>Liste complète
                                        </button>
                                        <button onClick={() => displayDayParticipations(value.id)} className={"triExposantsButton"}>
                                            <Moment format="dddd DD MMMM" locale="fr">{value.date}</Moment>
                                        </button>
                                        {value.childEvents.map((childEvent) => {
                                            return (
                                                <button key={childEvent.id} onClick={() => displayDayParticipations(childEvent.id)}
                                                        className={"triExposantsButton"}><Moment format="dddd DD MMMM" locale="fr">{value.childEvents[0].date}</Moment>
                                                </button>
                                            )
                                        })}
                                    </div>
                                :
                                section.sectionType.slug === "company" &&
                                    section.onPublic == true &&
                                    <div className={"triExposantsBox"}>
                                        <button onClick={() => displayAllParticipations(value.id)}
                                                className={"triExposantsButton"}>Liste complète
                                        </button>
                                        <button onClick={() => displayDayParticipations(value.id)} className={"triExposantsButton"}>
                                            <Moment format="dddd DD MMMM" locale="fr">{value.date}</Moment>
                                        </button>
                                        {value.childEvents.map((childEvent) => {
                                            return (
                                                <button key={childEvent.id} onClick={() => displayDayParticipations(childEvent.id)}
                                                        className={"triExposantsButton"}><Moment format="dddd DD MMMM" locale="fr">{value.childEvents[0].date}</Moment>
                                                </button>
                                            )
                                        })}
                                    </div>

                        )
                   })
                }
                {loading &&
                <LoaderElement />
                }
                {sections && loading == false &&
                sections.map(
                    section => {
                        if(section.sectionType.slug === "company"){
                            if(section.onPublic === true){
                                return(
                                    <div key={"participations"} className={"participationsContainer"}>
                                        {data &&
                                            data.sort(() => Math.random() - 0.5).map((participation) => {
                                                if(participation.premium == 1) {
                                                    return(<UniqueParticipation key={participation.id} participation={participation} />)
                                                }
                                            })
                                        }
                                        {data &&
                                            data.sort(() => Math.random() - 0.5).map((participation) => {
                                                if(participation.premium == 0 || participation.premium == null) {
                                                    return(<UniqueParticipation key={participation.id} participation={participation} />)
                                                }
                                            })
                                        }
                                    </div>
                                )
                            }else if(user){
                                if(user.roles.includes("ROLE_SUPER_ADMIN") || user.roles.includes("ROLE_ADMIN")){
                                    return(
                                        <div key={"participations"} className={"participationsContainer"}>
                                            {data &&
                                            data.map((participation) => {
                                                return (<UniqueParticipation key={participation.id} participation={participation} />)
                                            })
                                            }
                                        </div>
                                    )
                                }else{
                                    return(<p className={"inforamtifExposant"} className="text-center displayAt"> Venez découvrir la liste complète des exposants participant à cet événement à partir du <strong>{moment(value.online, 'YYYY-MM-DD’T’hh:mm:ss.SSSZ').format('DD/MM')}</strong></p>)
                                }
                            }else{
                                return(<p className={"inforamtifExposant"} className="text-center displayAt">Venez découvrir la liste complète des exposants participant à cet événement à partir du <strong>{moment(value.online, 'YYYY-MM-DD’T’hh:mm:ss.SSSZ').format('DD/MM')}</strong></p>)
                            }
                        }
                    }
                )}
                <p className={"inforamtifExposant"}>Vous représentez une entreprise, un centre de formation ou souhaitez participer à l’un de nos événements ?<br /> Contactez nous via ce formulaire : <Link to={"/Contact"}> Contact</Link></p>
            </>
        </React.Fragment>
    );
}