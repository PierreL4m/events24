import React, {useEffect, useState} from "react"
import 'moment/locale/fr';
import FileViewer from 'react-file-viewer';
import AuthService from "./Services/auth.service";

export default function RecallMail (props) {
    const [mailIsChecked, setMailIsChecked] = useState(null);
    const [smsIsChecked, setSmsIsChecked] = useState(null);
    useEffect(() => {
        if(document.getElementById("react-switch-sms").checked === true){
            setSmsIsChecked(true);
        }else{
            setSmsIsChecked(false);
        }

        if(document.getElementById("react-switch-mail").checked === true){
            setMailIsChecked(true);
        }else{
            setMailIsChecked(false);
        }
    }, []);

    const updateMail = (event) => {
        if(event.target.checked === true){
            var mail = true
        }else{
            var mail = false
        }
        AuthService.mailUpdate(
            mail,
            props.user.id
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
            setMailIsChecked(true);
        }else{
            setMailIsChecked(false);
        }
    }

    const updateSms = (event) => {
        if(event.target.checked === true){
            var sms = true
        }else{
            var sms = false
        }
        AuthService.smsUpdate(
            sms,
            props.user.id
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
            setSmsIsChecked(true);
        }else{
            setSmsIsChecked(false);
        }
    }
    return (
        <>

            <p className={"categoryProfil"} style={{fontSize:"10px",marginBottom:"60px",marginTop:"40px"}}>Préférences contact</p>
            <div className={"formGroup switchContainer"}>
                <label className={"labelCandidatForm"}>Recevoir des SMS</label>
                <input
                    className="react-switch-checkbox"
                    id={`react-switch-sms`}
                    type="checkbox"
                    defaultChecked={props.user.phoneRecall === true ? true : false}
                    onChange={updateSms}
                />
                <label
                    style={{background:props.user.candidateParticipations.length > 0 && smsIsChecked === true ? props.user.candidateParticipations[0].event.place.colors[0].code : "grey"}}
                    className="react-switch-label"
                    htmlFor={`react-switch-sms`}
                >
                    <i className="fa-solid fa-check" style={{color: "#ffffff",marginLeft:"10px"}}></i>
                    <span className={`react-switch-button`} />
                </label>
            </div>
            <div className={"formGroup switchContainer"}>
                <label className={"labelCandidatForm"}>Recevoir des mails</label>
                <input
                    className="react-switch-checkbox"
                    id={`react-switch-mail`}
                    type="checkbox"
                    defaultChecked={props.user.mailingRecall === true ? true : false}
                    onChange={updateMail}
                />
                <label
                    style={{background:props.user.candidateParticipations.length > 0 && mailIsChecked === true ? props.user.candidateParticipations[0].event.place.colors[0].code : "grey"}}
                    className="react-switch-label"
                    htmlFor={`react-switch-mail`}
                >
                    <i className="fa-solid fa-check" style={{color: "#ffffff",marginLeft:"10px"}}></i>
                    <span className={`react-switch-button`} />
                </label>
            </div>
        </>
    )
}