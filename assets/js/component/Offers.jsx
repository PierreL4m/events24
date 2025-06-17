import {unmountComponentAtNode} from 'react-dom'
import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {usePaginatedFetch, useUniqueFetch} from "./Hooks/CheckIfState.jsx";
import LoaderElement from "./Loader.jsx";
import UniqueJob from "./UniqueJob.jsx";
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import AuthService from "./Services/auth.service.js";
import ReactGA from "react-ga4";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';

export default function Offers (props) {
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/offers", title: "Offres" });
    }, []);
    const value = useContext(GlobalContext);
    const sections = useContext(SectionsContext);
    const [state, setState] = useContext(Context);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [filters, setFilters] = useState({
        sector:'',
        contract:'',
    });
    const [uniqueJobsTab, setUniqueJobsTab] = useState(null);
    const [uniqueContractsTab, setUniqueContractsTab] = useState(null);
    const getOffers = async () => {
        const { data } = await AuthService
            .get('/api/event/jobs/'+value.id)
            .then(res => {
                setLoading(false);
                return res;
            })
            .catch(e => {});
        setData(data);
    };
    useEffect(() => {
        getOffers();
    }, []);
    useEffect(() => {
        const getUnifyJobType = async () => {
            try {
                const jobType = [data.map(e => e['jobType'])];
                const uniqueJobs =[];
                const uniqueJobTab = jobType[0].filter(element => {
                    const isDuplicate = uniqueJobs.includes(element.name);
                    if (!isDuplicate) {
                        uniqueJobs.push(element.name);
                        return true;
                    }
                    return false;
                });
                setUniqueJobsTab(uniqueJobTab);
            } catch(err) {
                setError(err.message);
            }
        }
        getUnifyJobType();
    }, [data])

    useEffect(() => {
        const getUnifyContractType = async () => {
            try {
                const contractType = [data.map(e => e['contractType'])];
                const uniqueContract =[];
                const uniqueContractTab = contractType[0].filter(element => {
                    const isDuplicate = uniqueContract.includes(element.name);
                    if (!isDuplicate) {
                        uniqueContract.push(element.name);
                        return true;
                    }
                    return false;
                });
                setUniqueContractsTab(uniqueContractTab);
            } catch(err) {
                setError(err.message);
            }
        }
        getUnifyContractType();
    }, [data])

    const styles = {
        hr: {
            borderColor: value.place.colors[0].code,
        },
        selectors: {
            background: 'none',
            border: 'none',
            borderBottom:'solid 2px'+value.place.colors[0].code,
            paddingBottom:'10px'
        },
        none:{
            color:'#938f8c',
            marginTop:'25px',
            textAlign:'center',
        },
        title: {
            marginTop:'30px',
            marginBottom:'15px',
        },
        nbOffers:{
            textTransform: 'uppercase',
            fontWeight: 'bolder',
            color: 'grey',
        }
    };

    const addFilters = async (event) => {
        try {
            const value = event.target.value;
            setFilters({
                ...filters,
                [event.target.name]: value,
            });
        } catch(err) {
            setError(err.message);
        }
    }
    return (
        <React.Fragment>
            {loading &&
                <LoaderElement />
            }
            {data &&
                sections.map(
                    section => {
						if(section.sectionType.slug === "company"){
                            if(section.onPublic === true){
                                return(
                                    	<React.Fragment key={section.sectionType.slug}>
	                                        <div style={styles.title} className={"offersHeader"}>
	                                            <h2>Offre(s) d'emplois ou de formation</h2>
	                                            <hr style={styles.hr}/>
	                                        </div>
	                                        {/*{data &&*/}
	                                        {/*data.filter(job => job.contractType.name.includes(filters.contract)).filter(job => job.jobType.name.includes(filters.sector)).length > 1 ?*/}
	                                        {/*    <p style={styles.nbOffers}>*/}
	                                        {/*        {data.filter(job => job.contractType.name.includes(filters.contract)).filter(job => job.jobType.name.includes(filters.sector)).length} offres*/}
	                                        {/*    </p>*/}
	                                        {/*:*/}
	                                        {/*    <p style={styles.nbOffers}>{data.filter(job => job.contractType.name.includes(filters.contract)).filter(job => job.jobType.name.includes(filters.sector)).length} offre*/}
	                                        {/*    </p>*/}
	                                        {/*}*/}
	                                        <div className={"triExposantsBox"}>
	                                            <select onChange={addFilters} name="sector" id="sector-select" style={styles.selectors}>
	                                                <option value="">Filtrer par secteur d'activité</option>
	                                                <option value="">Tous</option>
	                                                {data &&
	                                                    uniqueJobsTab &&
	                                                         uniqueJobsTab
	                                                        .map(jobType => {
	                                                            return (
	                                                                <option key={jobType.name} value={jobType.name}>{jobType.name}</option>
	                                                            )
	                                                        })
	                                                }
	                                            </select>
	                                            <select onChange={addFilters} name="contract" id="contrat-select" style={styles.selectors}>
	                                                <option value="">Filtrer par type de contrat</option>
	                                                <option value="">Tous</option>
	                                                {data &&
	                                                uniqueContractsTab &&
	                                                uniqueContractsTab
	                                                    .map(contractType => {
	                                                        return (
	                                                            <option key={contractType.name} value={contractType.name}>{contractType.name}</option>
	                                                        )
	                                                    })
	                                                }
	                                            </select>
	                                        </div>
	                                        {data &&
	                                            data
	                                            .filter(job => job.contractType.name.includes(filters.contract))
	                                            .filter(job => job.jobType.name.includes(filters.sector))
	                                            .length ?
	                                                data
	                                                    .filter(job => job.contractType.name.includes(filters.contract))
	                                                    .filter(job => job.jobType.name.includes(filters.sector))
	                                                    .map(filteredJob => {
	                                                    return (
	                                                        <UniqueJob key={filteredJob.id} job={filteredJob} />
	                                                    )
	                                                })
	                                            :
	                                                <p className={"inforamtifExposant"}>Aucune offre ne correspond à votre recherche.<br/> Certains exposants n'ont pas communiqué leurs offres/postes.<br/> <b>Venez les découvrir directement sur les stands lors du salon.</b></p>
	
	                                        }
                                            <p className={"inforamtifExposant"}>Tous les exposants n'ont pas renseigné leurs offres à pourvoir. Retrouvez l'ensemble des offres directement sur place, lors du salon.</p>
                                        </React.Fragment>
                                    
                                )
                            }else{
                                return(<p className="text-center displayAt inforamtifExposant"> Venez découvrir la liste complète des exposants participant à cet événement à partir du <strong>{moment(data.online, 'YYYY-MM-DD’T’hh:mm:ss.SSSZ').format('DD/MM')}</strong></p>)
                            }
                        }
                    }
                )
            }
        </React.Fragment>
    );
}