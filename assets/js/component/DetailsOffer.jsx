import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route, Switch, useLocation, Redirect, useParams } from "react-router-dom";
import { useQuery } from "react-query";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LinkExposant from "./LinkExposant.jsx";
import ReactHtmlParser from 'react-html-parser';
import AuthService from "./Services/auth.service.js";
import ReactGA from "react-ga4";
import { Helmet } from "react-helmet";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function DetailsOffer (props) {
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/Offre/:idJob/:i", title: "Détail_offre" });
    }, []);
    let location = useLocation();
    const idJob = useParams().idJob;
    const value = useContext(GlobalContext);
    const id = (location.idJobProps != undefined) ? location.idJobProps.id : idJob;
    const [state, setState] = useContext(Context);
    const [job, setJob] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const getJob = async () => {
        const { data } = await AuthService
            .get('/api/job/'+id)
            .then(res => {
                setLoading(false);
                return res;
            });
        setJob(data);
    };
    useEffect(() => {
        getJob();
    }, []);
    const styles = {
        backButton: {
            backgroundColor: value.place.colors[0].code,
        },
        companyName: {
            textDecoration: 'underline',
            textDecorationColor: value.place.colors[0].code,
            fontSize:'25px',
            letterSpacing:'2px',
        },
        jobName: {
            color:value.place.colors[0].code,
        },
    };
    return (
        <React.Fragment>
            {job &&
                <>
                    <Helmet>
                        <html lang="en" />
                        <title>{job.name+" - "+value.type.fullName+" - "+value.place.city+" - "+moment(value.date).format('dddd DD MMMM')}</title>
                        <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+value.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                    </Helmet>
                <div className={"exposantPresentation"}>
                    <h1 style={styles.jobName} className={"jobName"}>{job.name}</h1>
                    <p className={"detailOfferSector"}>{job.jobType.name}</p>
                    <hr style={styles.backButton}/>
                    {ReactHtmlParser(job.presentation)}
                    <h2>Informations supplémentaires</h2>
                    <hr style={styles.backButton}/>
                    <p>Contrat : {job.contractType.name} {job.timeContract && '('+job.timeContract+')'}</p>
                    <p>Localisation : {job.city.name} {job.city.cp}</p>
                </div>
                </>
            }
        </React.Fragment>
    );
}