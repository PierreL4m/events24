import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route} from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx";
import AuthService from "./Services/auth.service.js";

export default function UniqueParticiation (props) {
    const value = useContext(GlobalContext);
    // FIXME unused ? const [participation, setparticipation] = useState(null);
    // FIXME unused ? const [loading, setLoading] = useState(true);
    // FIXME unused ? const [error, setError] = useState(null);
    // FIXME unused ? const [popJob, setPopJob] = useState(null);
    const [participation, setParticipation] = useState(props.participation);
    const [nbJobs, setNbJobs] = useState(null);
    const [loading, setLoading] = useState(true);
    // const getParticipation = async () => {
    //     const { data } = await AuthService
    //         .get('/api/participation/'+props.participation.id)
    //         .then(res => {
    //             setLoading(false);
    //             return res;
    //         });
    //     setParticipation(data);
    // };
    const getNbJobsParticipation = async () => {
        const { data } = await AuthService
            .get('/api/participation/nbjobs/'+props.participation.id)
            .then(res => {
                setLoading(false);
                return res;
            });
        setNbJobs(data);
    };
    // useEffect(() => {
    //     getParticipation();
    // }, []);
    useEffect(() => {
        getNbJobsParticipation();
    }, []);
    const styles = {
        backButton: {
            backgroundColor: value.place.colors[0].code,
        },
        infosExposants: {
            textTransform:'uppercase',
            fontSize:'11px',
            color:'grey',
            marginBottom:'10px',
        }
    };
    return (
        <React.Fragment>
        {participation &&
        <Link
              to={{pathname: "/Exposant/" + participation.id}}>
                <div className={"fakeBoxOrga"}>
                    <div className={"fakeBoxParticipation"}>
                        <div className={"cardCarousselParticipation"}>
                            <li style={participation.premium == true ? {borderColor: value.place.colors[0].code,borderStyle: 'solid',borderWidth:'2px'} : {borderColor: 'lightgray',borderStyle: 'solid',borderWidth:'1px'}} className="slide p-2">
                                {participation.organization.id === 1243 ?
                                    <p className={"organizationNameCovea"}>Covéa (MAAF, MMA et GMF)</p>
                                    :
                                    <p className={"organizationName"}>{participation.companyName}</p>
                                }
                                {participation.logo &&
                                    <div className={"logoBox"}>
                                        <img src={"uploads/"+participation.logo.path} alt=""/>
                                    </div>
                                }
                                <p style={styles.infosExposants} className={"infosExposants"}><img className={"pictoExpo"} src={"/images/dot.svg"} />{participation.city}</p>
                                {participation.sector !== null ?
                                    <p style={styles.infosExposants} className={"infosExposants"}><img className={"pictoExpo"} src={"/images/heart.svg"} />{participation.sector.name}</p>
                                    :
                                    <p style={styles.infosExposants} className={"infosExposants"}><img className={"pictoExpo"} src={"/images/heart.svg"} />Divers</p>
                                }
                                {participation.id == 8140 ?
                                    <p style={styles.infosExposants} className={"infosExposants"}>
                                        <img className={"pictoExpo"} src={"/images/cdi.svg"} />Formation, alternance, stage
                                    </p>
                                    :
                                    nbJobs !== null && nbJobs.length > 0 ?
                                        <p style={styles.infosExposants} className={"infosExposants"}>
                                            <img className={"pictoExpo"} src={"/images/cdi.svg"} />{nbJobs[0].name}
                                        </p>
                                        :
                                        <p style={styles.infosExposants} className={"infosExposants"}>
                                            <img className={"pictoExpo"} src={"/images/cdi.svg"} />CDI / CDD
                                        </p>
                                }
                                <Link style={styles.backButton} className={"buttonExposantOffer"}
                                      to={{pathname: "/Exposant/" + participation.id}}>
                                    Voir nos offres
                                </Link>
                            </li>
                        </div>
                    </div>
                </div>
        </Link>
        }
        </React.Fragment>
    );
}