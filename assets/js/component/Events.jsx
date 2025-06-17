import {unmountComponentAtNode} from 'react-dom'
import React, {useState,useEffect, Component, useContext, useRef} from "react"
import LoaderElement from "./Loader.jsx";
import UniqueEvent from "./UniqueEvent.jsx";
import { useLoading } from "./Context/LoadingContext";
import Footer from "./Footer.jsx";
import Flickity from 'react-flickity-component'
import AuthService from "./Services/auth.service.js";
import ReactGA from "react-ga4";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

export default function Events () {
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/Evenements", title: "Liste_evenements" });
    }, []);
    const [data, setData] = useState(null);
    const { loading, setLoading } = useLoading();
    const [error, setError] = useState(null);
    const [uniqueRegionTab, setUniqueRegionTab] = useState(null);
    const [user, setUser] = useState(null);
    const [logoType, setLogoType] = useState(null);
    const [filters, setFilters] = useState({
        region:'',
    });
    const getEvents = async () => {
        const { data } = await AuthService
            .get('/api/events')
            .then(res => {
                setLoading(false);
                return res;
            });
        setData(data);
    };
    useEffect(()=>{
        if(data !== null){
            if(data[0].type.fullName == "Recrutement Experts"){
                setLogoType("/images/logo_expert.svg");
            }else if(data[0].type.fullName == "Jobfest"){
                setLogoType("/images/logo_jobfest.svg");
            }else{
                setLogoType("/images/24H_logo.svg");
            }
        }
    }, [data])

    useEffect(() => {
        getEvents();
    }, []);
    useEffect(() => {
        const getUnifyRegion = async () => {
            try {
                const regionName = [data.map(e => e.place['region'])];
                const uniqueRegion =[];
                const uniqueRegionsTab = regionName[0].filter(element => {
                    const isDuplicate = uniqueRegion.includes(element.name);
                    if (!isDuplicate) {
                        uniqueRegion.push(element.name);
                        return true;
                    }
                    return false;
                });
                setUniqueRegionTab(uniqueRegionsTab);
            } catch(err) {
                setError(err.message);
            }
        }
        getUnifyRegion();
    }, [data])

    const [eventActive, changeState] = useState({
        activeObject: 0,
        data
    });
    function toggleActiveStyles(id) {
        if (eventActive.activeObject === id) {
            return "active";
        } else {
            return "inactive";
        }
    }

    function toggleActive(id) {
        var eventType = document.getElementsByClassName("active")[0].getAttribute("typee");
        if(eventType == "Recrutement Experts"){
            setLogoType("/images/logo_expert.svg");
        }else if(eventType == "Jobfest"){
            setLogoType("/images/logo_jobfest.svg");
        }else{
            setLogoType("/images/24H_logo.svg");
        }
        changeState({ ...eventActive, activeObject: id });
    }
    function setFlickityRef(e) {
        e.on( 'change', function( index )
        {
            var eventType = document.getElementsByClassName("is-selected")[0].getAttribute("typee");
            if(eventType == "Recrutement Experts"){
                setLogoType("/images/logo_expert.svg");
            }else if(eventType == "Jobfest"){
                setLogoType("/images/logo_jobfest.svg");
            }else{
                setLogoType("/images/24H_logo.svg");
            }
        })
;    }
    const addFilters = async (event) => {
        try {
            if(event.type == "click"){
                if(event.target.innerHTML != "Tout"){
                    var value = event.target.innerHTML;
                }else{
                    var value = "";
                }
            }else{
                var value = event.target.value;
            }
            setFilters({
                ...filters,
                region: value,
            });
        } catch(err) {
            setError(err.message);
        }
    }
    return (
        <React.Fragment>
            <>
                {logoType &&
                    <img className={"indexLogoEvent"} id={logoType == "/images/logo_expert.svg" ? "indexExpertLogo" : "index24Logo"} src={logoType} alt="l4m"/>
                }
                <select className={"mobileContent"} onChange={addFilters} name="region" id="region-select">
                    <option value="">Régions</option>
                    {data &&
                    uniqueRegionTab &&
                    uniqueRegionTab
                        .map(region => {
                            return (
                                <option key={region.name} value={region.name}>{region.name}</option>
                            )
                        })
                    }
                </select>
                <div className={"listRegion"}>
                    <p onClick={addFilters} name="region" className={"listRegionItem"} value="">Tout</p>
                    {data &&
                    uniqueRegionTab &&
                    uniqueRegionTab
                        .map(region => {
                            return (
                                <p key={region.name} onClick={addFilters} name="region" className={"listRegionItem"} value={region.name}>{region.name}</p>
                            )
                        })
                    }
                </div>
                {loading &&
                <LoaderElement />
                }
                <div className={"scrollable-list"}>
                    <div className="scrollable-list__scroll">
                        {data &&
                        data
                            .filter(event => event.place.region.name.includes(filters.region))
                            .map((event, index) => {
                                return event.parentEvent == null &&
                                    <div typee={event.type.fullName} key={event.id} onMouseOver={() => toggleActive(index)} className={"col-12 child gallery-cell "+toggleActiveStyles(index)}>
                                        <UniqueEvent key={index} event={event} />
                                    </div>
                            })
                        }
                    </div>
                </div>
                <>
                </>
                {data &&
                    <Flickity  flickityRef={setFlickityRef}>
                        {data &&
                            data
                                .filter(event => event.place.region.name.includes(filters.region))
                                .map((event, index) => {
                                return event.parentEvent == null &&
                                <div typee={event.type.fullName} key={event.id} className={"col-12 child gallery-cell "+toggleActiveStyles(index)}>
                                    <UniqueEvent key={index} event={event} />
                                </div>
                            })
                        }
                    </Flickity>
                }
                {data &&
                    <Footer />
                }
            </>
        </React.Fragment>
    );
}