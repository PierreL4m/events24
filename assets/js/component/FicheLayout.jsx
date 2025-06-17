import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route, Switch, useLocation, Redirect, useParams,useHistory } from "react-router-dom";
import { useQuery } from "react-query";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LinkExposant from "./LinkExposant.jsx";
import OffresList from "./OffresList.jsx";
import NavigationFiche from "./NavigationFiche.jsx";
import DetailsOffer from "./DetailsOffer.jsx";
import LoaderElement from "./Loader.jsx";
import ReactHtmlParser from 'react-html-parser';
import AuthService from "./Services/auth.service.js";
import ReactGA from "react-ga4";
import { Helmet } from "react-helmet";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import Linkify from 'react-linkify';

export default function FicheLayout () {
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/Exposant/:idOrga/:idJob?", title: "Fiche_exposant" });
    }, []);
    const { idOrga } = useParams();
    const { idJob } = useParams();
    let location = useLocation();
    const value = useContext(GlobalContext);
    const [state, setState] = useContext(Context);
    const [participations, setParticipations] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const getParticipations = async () => {
        const { data } = await AuthService
            .get('/api/participations/all/'+value.id)
            .then(res => {
                setLoading(false);
                return res;
            })
            .catch(e => {});
        setParticipations(data);
    };
    useEffect(() => {
        getParticipations();
    }, []);
    const styles = {
        companyName: {
            textDecorationColor: '#534b48',
            fontWeight: '700',
            fontSize: '1.5em',
            textTransform:'uppercase',
            marginBottom:'20px'
        },companyNameCovea: {
            textDecorationColor: '#534b48',
            fontWeight: '700',
            fontSize: '1.5em',
            marginBottom:'20px'
        },
    };
    let history = useHistory();
    return (
        <React.Fragment>
            {loading &&
                <LoaderElement />
            }
            {participations &&
            participations.map((participation) => {
                    if(participation.id == idOrga){
                        return (
                            <Router key={idOrga}>
                                <Helmet>
                                    <html lang="en" />
                                    <title>{participation.companyName+" - "+value.type.fullName+" - "+value.place.city+" - "+moment(value.date).format('dddd DD MMMM')}</title>
                                    <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+value.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                                </Helmet>
                                <Link onClick={history.goBack} className={"backToList"} to={"#"}>
                                    Revenir à la page précédente
                                </Link>
                                <Route exact path={"/Exposant/:idOrga"}>
                                    <NavigationFiche key={participation.id} participation={participation} array={participations}/>
                                </Route>
                                <div className={"layouFicheContent"}>
                                        <LinkExposant key={participation.id} participation={participation} />
                                        <div className={"exposantContent"}>
                                            <Route exact path={"/Exposant/:idOrga"}>
                                                <div className={"exposantPresentation"}>
                                                    {participation.organization.id === 1243 ?
                                                        <h1 style={styles.companyNameCovea}>Covéa (MAAF, MMA et GMF)</h1>
                                                    :
                                                        <h1 style={styles.companyName}>{participation.companyName}</h1>
                                                    }
                                                    {participation.youtube &&
                                                        <div className="video-responsive">
                                                            <iframe
                                                                width="853"
                                                                height="480"
                                                                src={`https://www.youtube.com/embed/`+participation.youtube.slice(-11)}
                                                                frameBorder="0"
                                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                                allowFullScreen
                                                                title="Embedded youtube"
                                                            />
                                                        </div>
                                                    }
                                                    <Linkify>
                                                        {ReactHtmlParser(participation.presentation)}
                                                    </Linkify>
                                                </div>
                                                <br/>
                                                <OffresList key={participation.id} participation={participation} />
                                            </Route>
                                            <Route exact path={"/Exposant/:idOrga/:idJob"}>
                                                <DetailsOffer />
                                            </Route>
                                        </div>
                                </div>
                            </Router>
                        )
                    }
                })
            }
        </React.Fragment>
    );
}