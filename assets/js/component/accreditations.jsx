import React, {useContext, useEffect, useState} from "react"
import {Link, Route, useHistory, useLocation} from "react-router-dom";
import 'moment/locale/fr';
import {GlobalContext} from "./Context/GlobalContext";
import { useParams } from "react-router-dom";
import AuthService from "./Services/auth.service";
import Form from "react-validation/build/form";
import FormField from "./FormField";
import CheckButton from "react-validation/build/button";
import {SectionsContext} from "./Context/SectionsContext";
import {Context} from "./Context/EventContext";
import LinkExposant from "./LinkExposant";
import Linkify from "react-linkify";
import ReactHtmlParser from "react-html-parser";
import OffresList from "./OffresList";
import DetailsOffer from "./DetailsOffer";
import axios from "axios";

export default function Accreditations () {
    const location = useLocation()
    const queryParameters = new URLSearchParams(location.search)
    const param = useParams();
    const value = useContext(GlobalContext);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [user, setUser] = useState(null);
    const [validation, setValidation] = useState(null);
    const [displayParticipation, setDisplayParticipation] = useState(queryParameters.get("participation"));
    const [waitingAccred, setWaitingAccred] = useState(false);
    const [doneAccred, setDoneAccred] = useState(false);
    const [already, setAlready] = useState(false);
    const [isLaoding, setIsLoading] = useState(false);
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
        },
        button:{
            width:"200px",
            lineHeight:"35px",
            borderRadius:"10px",
            backgroundColor: value.place.colors[0].code
        },
        buttonDone:{
            width:"55%",
            lineHeight:"35px",
            borderRadius:"10px",
            backgroundColor: value.place.colors[0].code
        },
        accredDone:{
            display: "flex",
            flexFlow: "column",
            alignItems: "center",
            width: "55%",
            border: "solid 2px",
            borderColor:value.place.colors[0].code,
            borderRadius: "25px",
            padding: "50px",
            color:"grey"
        }
    };
    const getParticipations = async () => {
        const { data } = await AuthService
            .get('/api/participations/all/'+value.id)
            .then(res => {
                setLoading(false);
                return res;
            })
            .catch(e => {});
        const names = [];
        const uniqueArray = [];
        data.map((item) => {
            if (!names.includes(item.companyName)) {
                names.push(item.companyName);
                uniqueArray.push(item);
            }
        });
        setData(uniqueArray);
    };
    useEffect(() => {
        getParticipations();
    }, []);
    const replayAccred = (e) => {
        setDoneAccred(false);
    };
    const handleAccred = (e) => {
        setIsLoading(true)
        e.preventDefault();
        Form.form.validateAll();
        AuthService.addAccred(
            displayParticipation,
            e.target.firstname.value,
            e.target.lastname.value,
            e.target.mail.value,
            e.target.phone.value
        ).then(
            (response) => {
                if (response.status == 201) {
                    if(response.data.mailErrorFormat || response.data.firstnameError || response.data.lastnameError || response.data.phoneError ||response.data.mailError || response.data.phonelenError){
                        setError(response.data)
                    }else{
                        setError(null)
                        setDoneAccred(true);
                    }
                }
            }
        );
    };
    const handleOnBlur = (e) => {
        axios.get('/api/accredByMail/' + e.target.value, {
                params: {
                    event: value.id
                }
        })
        .then(function (response) {
                if(response.data.length > 0){
                    setAlready(true);
                }else{
                    setAlready(false);
                }
            })
    }

    const getUser = async () => {
        const   user  = await AuthService.getUser();
        setUser(user);
    }
    useEffect(() => {
        getUser();
    }, []);
    return(
        <React.Fragment>
            {
                param.id == value.id ?
                data &&
                    user ?
                <>
                    <div className={"layouFicheContent"}>
                        <LinkExposant key={data.find(item => item.id == displayParticipation).id} participation={data.find(item => item.id == displayParticipation)} />
                        <div className={"exposantContent"}>
                            <div style={styles.title} className={"offersHeader"}>
                                <h2>Accréditations {value.type.fullName} - {value.place.city}</h2>
                                <hr style={styles.hr} />
                            </div>
                            <p style={{color:"grey"}}>Vous allez participer en tant qu'exposant à notre salon. <br/>Vous pouvez ajouter vos coordonnées pour recevoir votre accréditation et l'ensemble des informations pratiques.</p>
                            <div>
                                {doneAccred === false ?
                                    <Form
                                        className={"formAccred"}
                                        onSubmit={handleAccred}
                                        ref={c => {
                                            Form.form = c;
                                        }}
                                    >
                                        {validation &&
                                            <>
                                                <p style={{color: "green"}}>{validation}</p>
                                            </>

                                        }
                                        <div className={"FormInput"}>
                                            <label>Email</label>
                                            <input
                                                // onBlur={handleOnBlur}
                                                className={"form-control"}
                                                type={"email"}
                                                name={"mail"}
                                            />
                                            {already === true &&
                                                <p style={{color: "red"}}>Ce mail est déjà rataché à une accréditation.</p>
                                            }
                                            {error &&
                                                error.mailErrorFormat &&
                                                <p style={{color: "red"}}>{error.mailErrorFormat}</p>
                                            }
                                        </div>
                                        <div className={"FormInput"}>
                                            <label>Prénom</label>
                                            <input
                                                className={"form-control"}
                                                type={"text"}
                                                name={"firstname"}
                                            />
                                        </div>
                                        {error &&
                                            error.firstnameError &&
                                            <p style={{color: "red"}}>{error.firstnameError}</p>
                                        }
                                        <div className={"FormInput"}>
                                            <label>Nom</label>
                                            <input
                                                className={"form-control"}
                                                type={"text"}
                                                name={"lastname"}
                                            />
                                        </div>
                                        {error &&
                                            error.lastnameError &&
                                            <p style={{color: "red"}}>{error.lastnameError}</p>
                                        }
                                        <div className={"FormInput"}>
                                            <label>Numéro de portable</label>
                                            <input
                                                className={"form-control"}
                                                type={"text"}
                                                name={"phone"}
                                            />
                                        </div>
                                        {error &&
                                            error.phoneError &&
                                            <p style={{color: "red"}}>{error.phoneError}</p>
                                        }
                                        {error &&
                                            error.phonelenError &&
                                            <p style={{color: "red"}}>{error.phonelenError}</p>
                                        }
                                        {error &&
                                            error.mailError &&
                                            <p style={{color: "red"}}>{error.mailError}</p>
                                        }
                                        <div className="form-group">
                                            {data &&
                                                <button
                                                    disabled={isLaoding === false ? false : true}
                                                    style={styles.button} className="btn btn-primary btn-block">
                                                    <><i className="fa-solid fa-check" style={{
                                                        color: "white",
                                                        marginRight: "15px"
                                                    }}></i><span>Valider</span></>
                                                </button>
                                            }
                                        </div>
                                        {/*{form.validation && (*/}
                                        {/*    <div className="form-group">*/}
                                        {/*        <div className="alert alert-success" role="alert">*/}
                                        {/*            {form.validation}*/}
                                        {/*        </div>*/}
                                        {/*    </div>*/}
                                        {/*)*/}
                                        {/*}*/}
                                        <CheckButton
                                            style={{display: "none"}}
                                            ref={c => {
                                                CheckButton.checkBtn = c;
                                            }}
                                        />
                                    </Form>
                                    :
                                    <>
                                        <div style={styles.accredDone}>
                                            <p>Votre enregistrement a bien été pris en compte.</p><br/>
                                            <p style={{textAlign: "center"}}>Un mail a été envoyé à l'adresse renseignée
                                                dans lequel vous retrouverez votre accréditation.</p>
                                        </div>
                                        <button style={styles.buttonDone} onClick={replayAccred}
                                                className="btn btn-primary btn-block">Ajouter un collaborateur
                                        </button>
                                        {error && <p>{error}</p>}
                                    </>
                                }
                            </div>
                        </div>
                    </div>
                </>
                    :
                    <>
                        <p className="inforamtifExposant">Vous devez être connecté pour accéder à cette page</p>
                    </>
                    :
                    <p className="inforamtifExposant">Cette page ne correspond pas à un événement au quel vous participez !</p>
            }
        </React.Fragment>
    )
}