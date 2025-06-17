import {unmountComponentAtNode} from 'react-dom'
import React, {useState, useEffect, Component, useContext} from "react"
import {HashRouter as Router, Link, Route} from "react-router-dom";
import {usePaginatedFetch, useUniqueFetch} from "./Hooks/CheckIfState.jsx";
import LoaderElement from "./Loader.jsx";
import UniqueParticipation from "./UniqueParticipation.jsx";
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import AuthService from "./Services/auth.service.js";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function CarouselExposant (props) {
    const value = useContext(GlobalContext);
    const [state, setState] = useContext(Context);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const getParticipations = async () => {
        const { data } = await AuthService
            .get('/api/participations/event/'+value.id)
            .then(res => {
                setLoading(false);
                return res;
            });
        setData(data);
    };
    useEffect(() => {
        getParticipations();
    }, []);const
    styles = {
        backEvent: {
            borderRadius:"25px"
        },
    }
    return (
        <React.Fragment>
            <div>
                <ul id={"slides"}>
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
                </ul>
            </div>
                <Link style={styles.backEvent} id={"showExposantList"} to={"/Exposants"}>
                    Voir liste complète
                </Link>
        </React.Fragment>
    );
}