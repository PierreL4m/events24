import {HashRouter as Router, Link, Route, Switch, useLocation, Redirect} from "react-router-dom";
import React, {useState,useEffect, Component, useContext} from "react";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import LoaderElement from "./Loader.jsx";
import Navigation from "./Navigation.jsx";
import Banner from "./Banner.jsx";
import Home from "./Home.jsx";
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

function Layout () {
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
                    <DigitalBoost />
                    <div className={"navContainer"}>
                        <Navigation />
                        <UserContainer />
                    </div>
                    <img className={"indexLogoEvent"} id={data.type.fullName == "Recrutement Experts" ? "indexExpertLogoLayout" : "index24LogoLayout"} src={data.type.fullName == "Recrutement Experts" ? "/images/logo_expert.svg" : data.type.fullName == "Jobfest" ? "/images/logo_jobfest.svg" : "/images/24H_logo.svg"} alt="l4m"/>
                    <Banner key={data.id} event={data} />
                    <Route exact path={"/"}>
                        <Helmet>
                            <html lang="en" />
                            <title>{data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                            <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                        </Helmet>
                        <Home user={user} />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>
                    <Route exact path={"/Exposants"}>
                        <Helmet>
                            <html lang="en" />
                            <title>{"Exposants - "+data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                            <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                        </Helmet>
                        <Participations />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>
                    <Route exact path={"/Offres"}>
                        <Helmet>
                            <html lang="en" />
                            <title>{"Offres - "+data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                            <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                        </Helmet>
                        <Offers />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>

                    <Route exact path={"/Conferences"}>
                        <Helmet>
                            <html lang="en" />
                            <title>{"Conférences - "+data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                            <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                        </Helmet>
                        <Conferences />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>
                    <Route path={"/Exposant/:idOrga/:idJob?"}>
                        <FicheLayout />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>
                    <Route exact path={"/Offre/:idJob/:i"}>
                        <DetailsOffer />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>
                    <Route exact path={"/Infos"}>
                        <Helmet>
                            <html lang="en" />
                            <title>{"Infos - "+data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                            <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                        </Helmet>
                        <InfosPage />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>
                    <Route exact path={"/Contact"}>
                        <Helmet>
                            <html lang="en" />
                            <title>{"Contact - "+data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                            <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                        </Helmet>
                        <Contact />
                        {sections &&
                            sections.map(
                                section => {
                                    if(section.sectionType.slug === "registration"){
                                        if(section.onPublic === true){
                                            if(hasParticipation === true) {
                                                return(
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à l'événement</div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            }else{
                                                return(<SubscribeLayout key={section.sectionType.slug} isPublic={true} />)
                                            }
                                        }else{
                                            return(<SubscribeLayout key={section.sectionType.slug} isPublic={false} />)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>
                    <Route exact path={"/Inscription"}>
                        <Helmet>
                            <html lang="en" />
                            <title>{"Contact - "+data.type.fullName+" - "+data.place.city+" - "+moment(data.date).format('dddd DD MMMM')}</title>
                            <meta name="description" content={"Venez à la rencontre des 500 recruteurs lors de "+data.city+" ! L/’occasion de proposer directement votre candidature aux NOMBRE DE POSTES à pourvoir. Le DATE DU SALON + LIEU"} />
                        </Helmet>
                        <SubscribeForm />
                    </Route>
                    <Route exact path={"/check_subscribe_validation"}>
                        <CheckSubscribe event={data}/>
                        {sections &&
                            sections.map(
                                section => {
                                    if (section.sectionType.slug === "registration") {
                                        if (section.onPublic === true) {
                                            if (hasParticipation === true) {
                                                return (
                                                    <div id={"subscribeBaseText"}>
                                                        <div className={"flex-item"}></div>
                                                        <div className={"flex-item"}>Vous êtes déjà inscrit à
                                                            l'événement
                                                        </div>
                                                        <div className={"flex-item"}></div>
                                                    </div>
                                                )
                                            } else {
                                                return (
                                                    <SubscribeLayout key={section.sectionType.slug} isPublic={true}/>)
                                            }
                                        } else {
                                            return (<SubscribeLayout key={section.sectionType.slug} isPublic={false}/>)
                                        }
                                    }
                                }
                            )
                        }
                    </Route>

                    <Route exact path={"/accreditations/:id"}>
                        <Accreditations  user={user}/>
                    </Route>
                    {sections &&
                        sections.map(
                            section => {
                                if(section.sectionType.slug === "standards"){
                                    if(section.onPublic === true){
                                            return(
                                                <Partners />
                                            )
                                    }
                                }
                            }
                        )
                    }
                </Router>
                <Footer />
            </>
        }
            </SectionsContext.Provider>
        </GlobalContext.Provider>
    )
}

export default class LayoutElement extends Component {
    render(){
        return(<Layout/>)
    }
    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}