import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {GlobalContext} from "./Context/GlobalContext.jsx";
import ExposantSvg from "./Svg/ExposantSVG.jsx";
import FaceSvg from "./Svg/FaceSvg.jsx";
import OfferSvg from "./Svg/OfferSvg.jsx";
import ContractSvg from "./Svg/ContractSvg.jsx";
import CandidatSvg from "./Svg/CandidatSvg.jsx";
import MeetingSvg from "./Svg/MeetingSvg.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function KeyNumbers () {
    const value = useContext(GlobalContext);
    const DATE_OPTIONS = { year: 'numeric' };
    const styles = {
        colorPicto: {
            fill: value.place.colors[0].code,
        },
        backPicto: {
            backgroundColor: value.place.colors[2].code,
        },
        title: {
            textAlign: 'center',
            marginBottom:'30px',
            width:'100%',
            fontSize: '14px',
            textTransform: 'uppercase',
            fontXeight: '450',
            letterSpacing: '1px',
            color: 'grey',
            marginTop: '55px'
        },
        smallText: {
            color: value.place.colors[2].code,
            fontSize:"10px",
            paddingTop:"10px"
        }
    };
    return (
		<React.Fragment>
            <div className={"keyNumberPictoContainer"}>
                <h2 style={styles.title}>Les chiffres clés</h2>
                {value.keyNumbers?.exposants &&
                	<ExposantSvg number={value.keyNumbers.exposants}/>
            	}
                <FaceSvg />
                {value.keyNumbers?.offres &&
                	<OfferSvg number={value.keyNumbers.offres} />
            	}
                <ContractSvg />
                {value.keyNumbers?.candidats &&
                	<CandidatSvg number={value.keyNumbers.candidats} />
            	}
            	{value.keyNumbers?.entretiens &&
                	<MeetingSvg number={value.keyNumbers.entretiens} />
                }
            </div>
            <p style={styles.smallText}>* chiffres de nos salons {(new Date(value.date)).toLocaleDateString('fr-FR', DATE_OPTIONS) - 1}</p>
        </React.Fragment>
    );
}