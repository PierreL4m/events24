import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route, Switch, useLocation, Redirect, useParams,useHistory } from "react-router-dom";
import { useQuery } from "react-query";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LinkExposant from "./LinkExposant.jsx";
import OffresList from "./OffresList.jsx";
import ReactHtmlParser from 'react-html-parser';

export default function NavigationFiche (props) {
    function getIndexNext(id) {
        return props.array.findIndex(obj => obj.id === id);
    }
    let index = getIndexNext(props.participation.id);
    let history = useHistory();
    return (
        <React.Fragment>
            <div className={"navigationExposant"}>
                {index-1 > -1 &&
                    <Link className={"previousExposant"} to={{pathname:"/Exposant/"+props.array[index-1].id}}>
                        <i className="fa-solid fa-caret-left"></i> Entreprise Précedente
                    </Link>
                }
                {index+1 < props.array.length &&
                    <Link className={"nextExposant"} to={{pathname:"/Exposant/"+props.array[index+1].id}}>
                        Entreprise suivante <i className="fa-solid fa-caret-right"></i>
                    </Link>
                }
                </div>
        </React.Fragment>
    );
}