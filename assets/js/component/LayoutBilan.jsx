import {HashRouter as Router, Link, Route, Switch, useLocation, Redirect} from "react-router-dom";
import React, {useState,useEffect, Component, useContext} from "react";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx";
import Navigation from "./Navigation.jsx";
import BannerBilan from "./BannerBilan.jsx";
import HomeBilan from "./HomeBilan.jsx";
import AudioBilan from "./AudioBilan.jsx";
import VideoBilan from "./VideoBilan.jsx";
import DocBilan from "./DocBilan.jsx";
import Participations from "./Participations.jsx";
import UserContainer from "./UserContainer.jsx";
import Partners from "./Partners.jsx";
import Offers from "./Offers.jsx";
import InfosPage from "./InfosPage.jsx";
import Contact from "./Contact.jsx";
import Conferences from "./Conferences.jsx";
import DetailsOffer from "./DetailsOffer.jsx";
import FicheLayout from "./FicheLayout.jsx";
import Footer from "./Footer.jsx";
import SubscribeLayout from "./SubscribeLayout.jsx";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import AuthService from "./Services/auth.service.js";
import { Helmet } from "react-helmet";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import ScrollToTop from "./Services/scrollToTop.js";
import SubscribeForm from "./SubscribeForm";
import CheckSubscribe from "./checkSubscribe";
import Accreditations from "./accreditations"
import DigitalBoost from "./DigitalBoost";
import Concept from "./Concept";

function LayoutBilan () {
    let location = useLocation();
    const id = (location.idEventProps != undefined) ? location.idEventProps.id : document.getElementById('root').getAttribute("data-id");
    const [data, setData] = useState(null);
    const [concept, setConcept] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [sections, setSections] = useState(null);
    const [participations, setParticipations] = useState(null);
    const [user, setUser] = useState(null);
    const getParticipations = async () => {
        const participations  = await AuthService
            .get('/api/candidate/participations')
            .then(res => {
                if(res) {
                    setParticipations(res.data);
                }
                return res;
            })
            .catch(e => {});
    };

    useEffect(async () => {
            let u = await AuthService.getUser();
            if(u) {
                setUser(u);
                getParticipations();
            }
        }
        , []
    );

    if(participations != null){
        var hasParticipation = participations.some(participation => participation.event.id == id);
    }

    useEffect(() => {
        const getData = async () => {
            try{
                const response = await AuthService.get(
                    '/api/event/'+id
                );
                let actualData = response.data;
                setData(actualData);
            }finally {
                setLoading(false);
            }
        }
        getData();
    }, [])

    useEffect(() => {
        const getSections = async () => {
            try {
                const response = await AuthService.get(
                    '/api/sections/'+id
                );
                let actualData = response.data;
                setSections(actualData);
                setError(null);
            } catch(err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        }
        getSections();
    }, [])
    return (
        <GlobalContext.Provider value={data} test={1}>
            <SectionsContext.Provider value={sections}>
                {loading &&
                    <LoaderElement />
                }
                {data &&
                    <>
                        <Router>
                            <ScrollToTop />
                            <img className={"indexLogoEvent"} id={data.type.fullName == "Recrutement Experts" ? "indexExpertLogoLayout" : "index24LogoLayout"} src={data.type.fullName == "Recrutement Experts" ? "/images/logo_expert.svg" : data.type.fullName == "Jobfest" ? "/images/logo_jobfest.svg" : "/images/24H_logo.svg"} alt="l4m"/>
                            <BannerBilan key={data.id} event={data} />
                            <Route exact path={"/"}>
                                <Helmet>
                                    <html lang="en" />
                                    <title>{data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                                    <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                                </Helmet>
                                {sections &&
                                    sections.map(
                                        section => {
                                            if(section.sectionType.slug === "concept"){
                                                return(<Concept key={section.sectionType.slug} concept={section}/>)
                                            }
                                        }
                                    )
                                }
                                {data.bilanFiles.filter(file => file.bilan_file_type.id == 3).length > 0 &&
                                    <DocBilan interview={data.bilanFiles.filter(file => file.bilan_file_type.id == 3)} />
                                }
                                {data.bilanFiles.filter(file => file.bilan_file_type.id == 1).length > 0 &&
                                    <AudioBilan interview={data.bilanFiles.filter(file => file.bilan_file_type.id == 1)} />
                                }
                                {data.bilanFiles.filter(file => file.bilan_file_type.id == 2).length > 0 &&
                                    <VideoBilan interview={data.bilanFiles.filter(file => file.bilan_file_type.id == 2)} />
                                }
                                <HomeBilan />
                            </Route>
                        </Router>
                        <Footer />
                    </>
                }
            </SectionsContext.Provider>
        </GlobalContext.Provider>
    )
}

export default class LayoutBilanElement extends Component {
    render(){
        return(<LayoutBilan/>)
    }
    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}