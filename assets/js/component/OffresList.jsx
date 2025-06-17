import {unmountComponentAtNode} from 'react-dom'
import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {usePaginatedFetch, useUniqueFetch} from "./Hooks/CheckIfState.jsx";
import LoaderElement from "./Loader.jsx";
import UniqueJob from "./UniqueJob.jsx";
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import AuthService from "./Services/auth.service.js";

export default function OffresList (props) {
    const value = useContext(GlobalContext);
    const [state, setState] = useContext(Context);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const getOffers = async () => {
        const { data } = await AuthService
            .get('/api/participation/jobs/'+props.participation.id)
            .then(res => {
                setLoading(false);
                return res;
            })
            .catch(e => {console.log(e);});
        setData(data);
    };
    useEffect(() => {
        getOffers();
    }, []);
    const styles = {
        hr: {
            borderColor: value.place.colors[0].code,
        },
    };
    return (
        <React.Fragment>
            {loading &&
                <LoaderElement />
            }
            {data &&
                data.length > 0 &&
                    <>
                    <div className={"offersHeader"}>
                    <h2>Offre(S) d'emplois ou de formation</h2>
                    <hr style={styles.hr}/>
                    </div>
                    </>                
            }
            {data &&
                data.length > 0 ?
                data.map((job) => {
                    return (
                        <UniqueJob key={job.id} job={job} />
                    )
                })
                :
                <p className={" inforamtifExposant"}>Cet exposant n'a pas encore communiqué ses offres. Venez les découvrir directement sur le stand lors du salon.</p>
            }
        </React.Fragment>
    );
}