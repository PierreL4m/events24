import React, {useState, useEffect, Component, useContext} from "react";
import Form from "react-validation/build/form";
import Input from "react-validation/build/input";
import CheckButton from "react-validation/build/button";
import FormField from "./FormField.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import {Context} from "./Context/EventContext.jsx";
import AuthService from "./Services/auth.service.js";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import {Link} from "react-router-dom";

export default function PreRegister () {

    const value = useContext(GlobalContext);
    const [form, setForm] = useState({
        email: [],
        loading: false,
        message: "",
        validation:""
    });

    function handleSubscribe(e){
        e.preventDefault();
        setForm({
            loading: true,
            email: []
        });
        Form.form.validateAll();
        AuthService.preregister(value.id, form.email).then(
            (response) => {
                if(response.status == 201){
                    setForm({
                        validation: "Votre mail a bien été enregistré un mail vous sera envoyé lors de l'ouverture des inscriptions"
                    });
                }
            }
        )
    };
    function handleFormChange(e) {
        setForm({
            email: [e.target.value]
        });
    }

    return (
        <>
            <p className={"preSubText"}>
                Vous êtes candidat, les inscriptions au salon seront possibles à partir du {moment(value.online).format('dddd DD MMMM')}.<br/><br/>
                En renseignant votre adresse mail ici, vous serez averti(e) dès que vous pourrez vous inscrire.
            </p>
            <p style={{textAlign:"center"}}>Entreprise ou centre de formation, vous souhaitez devenir exposant ? 
                <Link to={"/Contact"}>
                    Cliquez ici.
                </Link>
            </p>
            <p className={"validationPresubText"}>{form.validation}</p>
            <Form onSubmit={handleSubscribe} ref={c => {Form.form = c;}}>
                <div className="form-group">
                    <label htmlFor="mail">Email :</label>
                    <input
                        className={"form-control"}
                        type={'email'}
                        name={'email'}
                        onChange={handleFormChange}
                    />
                        <button style={{display:"block",margin:"auto",marginTop:"15px"}} type="submit" className="btn btn-success">
                            Enregistrer
                        </button>
                </div>
            </Form>
        </>
    );
}

