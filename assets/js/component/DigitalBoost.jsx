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

export default function DigitalBoost (props) {
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
    const [currentPremium, setCurrentPremium] = useState(null);
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
    const getPremium = async () => {
        const { data } = await AuthService
            .get('/api/participations/premium/event/'+value.id)
            .then(res => {
                setLoading(false);
                return res;
            })
            .catch(e => {});
        setData(data);
    };
    useEffect(() => {
        getPremium();
    }, []);
    useEffect(() => {
        let i = 1;
        const interval = setInterval(() => {
            if(i == data.length){
                i = 0;
                setCurrentPremium(data[i]);
                i++;
            }else{
                setCurrentPremium(data[i]);
                i++;
            }
        }, 10000);
        return () => clearInterval(interval);
    }, [data]);
        function displayPub() {
            document.getElementById('pubDigital').style.display = 'none';
        }
    return (
        <React.Fragment>
            {data &&
                data.length > 0 &&
                    data[0].pub !== null &&
                    <div id={"pubDigital"} style={{position:"relative"}} className={"digitalBoostContainer"}>
                        <img onClick={displayPub} style={{position:"absolute", zIndex:"10", right:"25px", top:"20px"}} src={"/images/FERMER.png"}/>
                        {currentPremium === null ?
                            <>
                                <Link className={"nav-link pubDesktopLink"} to={"/Exposant/"+data[0].id}>
                                    <img src={"/uploads/pub/"+data[0].pub}/>
                                </Link>
                                <Link className={"nav-link pubMobileLink"} to={"/Exposant/"+data[0].id}>
                                    <img src={"/uploads/pub/"+data[0].pubMobile}/>
                                </Link>
                            </>
                            :
                            <>
                                <Link className={"nav-link pubDesktopLink"} to={"/Exposant/"+currentPremium.id}>
                                    <img src={"/uploads/pub/"+currentPremium.pub}/>
                                </Link>
                                <Link className={"nav-link pubMobileLink"} to={"/Exposant/"+currentPremium.id}>
                                    <img src={"/uploads/pub/"+currentPremium.pubMobile}/>
                                </Link>
                            </>
                        }
                </div>
            }
        </React.Fragment>
    );
}