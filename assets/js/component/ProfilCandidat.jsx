import React, {useState, useEffect} from "react"
import {Link} from "react-router-dom"
import AuthService from "./Services/auth.service.js";
import 'moment/locale/fr';
import LogOutButton from "./LogOutButton";
import CandidatForm from "./candidatForm.js";
import MyEvents from "./MyEvents";
import MyCv from "./myCV";
import RecallMail from "./recallMail";
import UpcomingEvents from "./upcomingEvents";
import DeleteCandidat from "./deleteCandidat";
import Footer from "./Footer";
import MyNextEvent from "./MyNextEvent";

export default function ProfilCandidat () {
    const [user, setUser] = useState(null);
    const [nextEvent, setNextEvent] = useState(null);
    const getUser = async () => {
        const   user  = await AuthService.getUser();
        setUser(user);
    }

    useEffect(() => {
        getUser();
    }, []);

    useEffect(() => {
        if(user !== null){
            let nextTimeData = [];
            const sortedData = user.candidateParticipations.sort((a, b) => new Date(a.event.date) - new Date(b.event.date))
            sortedData.forEach((element) => {
                if (new Date().setHours(0)  <= new Date(element.event.date)) {
                    nextTimeData.push(element)
                }
            })
            setNextEvent(nextTimeData);
        }
    }, [user]);
    return (
        <>

            {user &&
                <>
                    <div className={"profilNav"}>
                        <Link style={{padding:"0"}} data-toggle="collapse" data-target="#navbarNavAltMarkup" className={"nav-link"} to={"/"}>
                            <img style={{width:"20px"}} src={"/images/home.svg"} />
                        </Link>
                        <div className={"disconnectCandidat"}>
                            <img className={"loginImg"} src={"/images/connection.svg"} />{user.firstname}
                            <p style={{margin:"0"}}>|</p>
                            <LogOutButton />
                        </div>
                    </div>
                    <div className={"logoEventCandidat"}>
                        <img className={"indexLogoEvent"} src={"/images/24H_logo.svg"} alt="l4m"/>
                        <p style={{color:"#575756"}}>|</p>
                        <img className={"indexLogoEvent"} src={"/images/logo_jobfest.svg"} alt="l4m"/>
                        <p style={{color:"#575756"}}>|</p>
                        <img className={"indexLogoEvent"} src={"/images/logo_expert.svg"} alt="l4m"/>
                    </div>
                    <p style={{fontSize:"10px",color:"#575756"}}>Bienvenue</p>
                    <div style={{display:"flex",gap:"10px"}}>
                        <p className={"firstAndLastname"} style={{textTransform:"capitalize"}}>{user.firstname}</p><p className={"firstAndLastname"} style={{textTransform:"uppercase"}}>{user.lastname}</p>
                    </div>
                    <div className={"candidatProfilContainer"}>
                        <div className={"personalInfo"}>
                            {nextEvent &&
                                nextEvent.length > 0 &&
                                <MyNextEvent user={user} event={nextEvent[0]}/>
                            }
                            <CandidatForm user={user}/>
                            <MyCv user={user}/>
                            <RecallMail user={user}/>
                        </div>
                        <div className={"myEvents"}>
                            {nextEvent &&
                                nextEvent.length > 0 &&
                                <MyEvents user={user} event={nextEvent} />
                            }
                            <UpcomingEvents parentFunction={getUser} user={user}/>
                        </div>
                    </div>
                    <DeleteCandidat user={user}/>
                    <Footer/>
                </>
            }
        </>
    )
}