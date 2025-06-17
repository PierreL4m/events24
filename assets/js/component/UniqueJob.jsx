import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {BrowserRouter as Router, Link, Route, useHistory} from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx";

export default function UniqueJob (props) {
	const history = useHistory();
	
	const value = useContext(GlobalContext);
    const styles = {
        backButton: {
            backgroundColor: value.place.colors[0].code,
            color:'white',
        },
        img: {
            width: '100%',
            position: 'relative',
            top: '50%',
            transform: 'translateY(-50%)',
        },
        jobName: {
            color:"rgb(63, 157, 152)",
        },
    };
    return (
        <React.Fragment>
            <div className={"offerBox"} onClick={() => {history.push("/Exposant/"+props.job.participation.id+"/"+props.job.id)}}>
                <div className={"logoBox"}>
                    <img src={"uploads/"+props.job.participation.logo.path} alt=""/>
                </div>
                <div className={"infoBoxOffer"}>
                    <p style={styles.jobName} className={"offerName"}>{props.job.name}</p>
                    {props.job.participation.organization.id === 1243 ?
                        <p className={"offerNameCovea"}>Covéa (MAAF, MMA et GMF)</p>
                        :
                        <p className={"offerName"}>{props.job.participation.companyName}</p>
                    }
                    <p className={"offerSector"}><img className={"pictoExpo"} src={"/images/heart.svg"} />{props.job.jobType.name}</p>
                    <p className={"offerContract"}><img className={"pictoExpo"} src={"/images/cdi.svg"} /> Contrat : {props.job.contractType.name} {props.job.timeContract && '('+props.job.timeContract+')'}</p>
                    <p className={"offerCity"}><img className={"pictoExpo"} src={"/images/dot.svg"} />{props.job.city.name} {props.job.city.cp}</p>
                    <div className={"linkOffer"}>
                        <Link style={styles.backButton} className={"buttonOffer"} to={{pathname:"/Exposant/"+props.job.participation.id+"/"+props.job.id, idJobProps:{id:props.job.id}}}>
                            Ça m'interesse
                        </Link>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}
