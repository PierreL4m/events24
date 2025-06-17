import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route, Switch, useLocation, Redirect, useParams,useHistory } from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import SocialContainer from "./SocialContainer.jsx";
import AuthService from "./Services/auth.service.js";

export default function LinkExposant (props) {
    const participation = props.participation;
    const [nbJobs, setNbJobs] = useState(null);
    const [loading, setLoading] = useState(true);
    const styles = {
        infosExposants: {
            textTransform:'uppercase',
            fontSize:'11px',
            color:'grey',
            marginTop:'10px',
            marginBottom:'10px',
        }
    };
    const getNbJobsParticipation = async () => {
        const { data } = await AuthService
            .get('/api/participation/nbjobs/'+participation.id)
            .then(res => {
                setLoading(false);
                return res;
            });
        setNbJobs(data);
    };
    useEffect(() => {
        getNbJobsParticipation();
    }, []);
    return (
        <React.Fragment>
            <div className="exposantBox">
                <Link to={{pathname: "/Exposant/" + participation.id}}>
                    <div className={"logoBox"}>
                            <img src={"/uploads/"+participation.logo.path} alt=""/>
                    </div>
                </Link>
                <SocialContainer key={participation.id} participation={participation} />
                <div className={"pictoExposant"}>
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
                </div>
            </div>
        </React.Fragment>
    );
}