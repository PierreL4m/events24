import React, {useEffect, useState} from "react"
import 'moment/locale/fr';
import AuthService from "./Services/auth.service";
import axios from "axios";
import { AutoComplete } from 'primereact/autocomplete';

export default function CandidatForm (props) {
    const [degrees, setDegrees] = useState(null);
    const [mobilities, setMobilities] = useState(null);
    const [sectors, setSectors] = useState(null);
    const [cities, setCities] = useState([]);
    const [selectCity, setSelectCity] = useState(null);
    const [wokingIsChecked, setWorkingIsChecked] = useState(null);
    const [user, setUser] = useState(null);
    const getUser = async () => {
        const   user  = await AuthService.getUser();
        setUser(user);
    }

    useEffect(() => {
        getUser();
    }, []);

    const search = (event) => {
        axios
            .get('/api/filtered/cities?filter='+event.query)
            .then(res => {
                setCities(res.data);
                return res;
            });
    }
    const getDegrees = async () => {
        await AuthService
            .get('/api/degree')
            .then(res => {
                setDegrees(res.data);
            });
    };

    const getMobilities = async () => {
        await AuthService
            .get('/api/mobility')
            .then(res => {
                setMobilities(res.data);
            });
    };

    const getSectors = async () => {
        await AuthService
            .get('/api/sector')
            .then(res => {
                setSectors(res.data);
            });
    };
    const updateWorking = (event) => {
        if(event.target.checked === true){
            var working = true
        }else{
            var working = false
        }
        AuthService.workingUpdate(
            working,
            user.id
        ).then(
            (response) => {
                if (response.status == 201) {
                    AuthService.login(
                        e.target.email.value,
                        e.target.plainPassword.value
                    ).then(
                        (response) => {
                            setloadingSubscirbe(false);
                            window.location.reload(true);
                        }
                    );
                }else{
                    setloadingSubscirbe(false);
                    handleErrors(response);
                }
            }
        );
        if(event.target.checked === true){
            setWorkingIsChecked(true);
        }else{
            setWorkingIsChecked(false);
        }
    }
    useEffect(() => {
        getDegrees();
        getMobilities();
        getSectors();
    }, []);
    const updateInfos = (event) => {
        event.preventDefault();
        AuthService.infosUpdate(
            event.target.phone.value,
            event.target.mail.value,
            [event.target.diplome.value],
            [event.target.mobility.value],
            event.target.city.value,
            [event.target.sector.value],
            user.id
        ).then(
            (response) => {
                if (response.status == 201) {
                    AuthService.login(
                        e.target.email.value,
                        e.target.plainPassword.value
                    ).then(
                        (response) => {
                            setloadingSubscirbe(false);
                            window.location.reload(true);
                        }
                    );
                }else{
                    setloadingSubscirbe(false);
                    handleErrors(response);
                }
            }
        );
    }
    return (
        <>
        {user &&
            <>
                <p className={"categoryProfil"} style={{fontSize:"10px",marginBottom:"60px",marginTop:"15px"}}>Mes informations</p>
                <form className={"formCandidat"} onSubmit={updateInfos} action={'#'}>
                    <div className={"formGroup"}>
                        <label className={"labelCandidatForm"}>Téléphone</label>
                        <input
                            className={"form-control inputCandidatForm"}
                            type={"text"}
                            name={"phone"}
                            defaultValue={user.phone}
                        />
                    </div>
                    <div className={"formGroup"}>
                        <label className={"labelCandidatForm"}>Email</label>
                        <input
                            className={"form-control inputCandidatForm"}
                            type={"text"}
                            name={"mail"}
                            defaultValue={user.email}
                        />
                    </div>

                    <div className={"formGroup"}>
                        <label className={"labelCandidatForm"}>Niveau</label>
                        <select style={{color:user.candidateParticipations.length > 0 && user.degree === null && user.candidateParticipations[0].event.place.colors[0].code}} id={"selectCity"} className={user.degree === null ? "form-control inputCandidatForm emptySelectCandidat" : "form-control inputCandidatForm"} name={"diplome"} defaultValue={user.degree !== null && user.degree.name}>
                            {user.degree === null ? <option>Non renseigné</option> : <option value={user.degree.id}>{user.degree.name}</option>}
                            {degrees &&
                                degrees.map((degree, index) => {
                                    return(
                                        <option value={degree.id} >{degree.name}</option>
                                    )
                                })}
                        </select>
                    </div>

                    <div className={"formGroup"}>
                        <label className={"labelCandidatForm"}>Mobilité</label>
                        <select style={{color:user.candidateParticipations.length > 0 && user.mobility === null && user.candidateParticipations[0].event.place.colors[0].code}} className={user.mobility === null ? "form-control inputCandidatForm emptySelectCandidat" : "form-control inputCandidatForm"} name={"mobility"} defaultValue={user.mobility !== null && user.mobility.name}>
                            {user.mobility === null ? <option>Non renseigné</option> : <option value={user.mobility.id}>{user.mobility.name}</option>}
                            {mobilities &&
                                mobilities.map((mobility, index) => {
                                    return(
                                        <option value={mobility.id} >{mobility.name}</option>
                                    )
                                })}
                        </select>
                    </div>
                    <div className={"formGroup"}>
                        <label className={"labelCandidatForm"}>Ville</label>
                        <AutoComplete name={"city"} field="name" defaultValue={user.city} value={user.city === null ? selectCity === null ? "Non renseigné" : selectCity : selectCity === null ? user.city.name : selectCity} suggestions={cities} completeMethod={search} onChange={(e) => setSelectCity(e.value)} />
                    </div>
                    <div className={"formGroup"}>
                        <label className={"labelCandidatForm"}>En Poste</label>
                        <input
                            className="react-switch-checkbox"
                            id={`react-switch-workings`}
                            type="checkbox"
                            defaultChecked={user.working === true ? true : false}
                            onChange={updateWorking}
                        />
                        <label
                            style={{background:user.candidateParticipations.length > 0 && wokingIsChecked === true ? user.candidateParticipations[0].event.place.colors[0].code : "grey"}}
                            id={"switchLabel"}
                            className="react-switch-label"
                            htmlFor={`react-switch-workings`}
                        >
                            <i className="fa-solid fa-check" style={{color: "#ffffff",marginLeft:"10px"}}></i>
                            <span className={`react-switch-button`} />
                        </label>
                    </div>
                    <div style={{flex:"100%"}} className={"formGroup"}>
                        <label className={"labelCandidatForm"}>Secteur recherché</label>
                        <select style={{color:user.candidateParticipations.length > 0 && user.wantedJob === null && user.candidateParticipations[0].event.place.colors[0].code}} className={user.wantedJob === null ? "form-control inputCandidatForm emptySelectCandidat" : "form-control inputCandidatForm"} name={"sector"} defaultValue={user.wantedJob !== null && user.wantedJob}>
                            {user.wantedJob === null ? <option>Non renseigné</option> : <option>{user.wantedJob}</option>}
                            {sectors &&
                                sectors.map((sector, index) => {
                                    return(
                                        <option  value={sector.name}>{sector.name}</option>
                                    )
                                })}
                        </select>
                    </div>
                    <button style={{display:"none"}} type="submit">Submit</button>
                    <button type="submit" style={{backgroundColor:user.candidateParticipations.length > 0 && user.candidateParticipations[0].event.place.colors[0].code}} className="validateCandidatInfosBUtton">
                        <span>Valider les informations</span>
                    </button>
                </form>
            </>
        }
        </>
    )
}