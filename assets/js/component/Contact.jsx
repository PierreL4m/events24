import {unmountComponentAtNode} from 'react-dom'
import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, Link, Route} from "react-router-dom"
import Form from "react-validation/build/form";
import {usePaginatedFetch, useUniqueFetch} from "./Hooks/CheckIfState.jsx";
import LoaderElement from "./Loader.jsx";
import UniqueParticipation from "./UniqueParticipation.jsx";
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import {SectionsContext} from "./Context/SectionsContext.jsx";
import AuthService from "./Services/auth.service.js";
import ReactGA from "react-ga4";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import ScriptTag from 'react-script-tag';

export default function Contact (props) {
    ReactGA.initialize([
        {
            trackingId: "G-90VVBCB1Z9",
        },
    ]);
    useEffect(() => {
        ReactGA.send({ hitType: "pageview", page: "/Contact", title: "Contact" });
    }, []);
    const value = useContext(GlobalContext)
    const [formHub, setFormHub] = useState(null)
    const [form, setForm] = useState({
        from:"",
        name:"",
        firstName:"",
        email:[],
        phone:"",
        message:"",
        loading: false,
        validation:""
    });
    const styles = {
        hr: {
            borderColor: value.place.colors[0].code,
        },
        backButton: {
            backgroundColor: value.place.colors[0].code,
        },
        inputStyle: {
            marginTop:"10px",
        },
    };
    function handleContact(e){
        e.preventDefault();
        setForm({
            from:"",
            name:"",
            firstName:"",
            email:[],
            phone:"",
            message:"",
            loading: false
        });
        Form.form.validateAll();
        AuthService.contact(value.id, form.from, form.name, form.firstName, form.email, form.phone, form.message).then(
            (response) => {
                if(response.status == 201){
                    setForm({
                        validation: "Votre mail a bien été envoyé !"
                    });
                }
            }
        )
    };

    const handleFormChange = (event) => {
        const { name, value } = event.target;
        const updatedForm = {
            ...form,
            [name]: value
        };
        setForm(updatedForm);
    };
    const handleFormChangeEmail = (event) => {
        const { name, value } = event.target;
        const updatedForm = {
            ...form,
            email: [value]
        };
        setForm(updatedForm);
    };
    return (
        <React.Fragment>
            <div style={styles.title} className={"offersHeader"}>
                <h2>Nous contacter</h2>
                <hr style={styles.hr}/>
            </div>
            <Form onSubmit={handleContact} ref={c => {
                Form.form = c;
            }}>
                <div id="contact">
                    <div className="form-group">
                        <label className="required" htmlFor="from">
                            L'objet de ma demande
                        </label>
                        <select onChange={handleFormChange} style={styles.inputStyle} id="contact_from" name="from"
                                className="form-control">
                            <option value="Demande d'informations">
                                Quel est l'objet de votre demande ?
                            </option>
                            <option value="Demande d'informations">
                                Je suis visiteur
                            </option>
                            <option value="Contact &quot;Je souhaite devenir exposant&quot;">
                                Je souhaite devenir exposant
                            </option>
                        </select>
                    </div>
                    <div className="form-group">
                        <label htmlFor="contact_name" className="required">Nom </label>
                        <input onChange={handleFormChange} style={styles.inputStyle} type="text" id="contact_name"
                               name="name" required="required" className="form-control"/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="contact_firstName" className="required">Prénom </label>
                        <input onChange={handleFormChange} style={styles.inputStyle} type="text"
                               id="contact_firstName" name="firstName" required="required"
                               className="form-control"/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="contact_phone" className="required">Téléphone </label>
                        <input onChange={handleFormChange} style={styles.inputStyle} type="tel" id="contact_phone"
                               name="phone" required="required" className="form-control"/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="contact_email" className="required">Email </label>
                        <input onChange={handleFormChangeEmail} style={styles.inputStyle} type="email"
                               id="contact_email" name="email" required="required" className="form-control"/>
                    </div>
                    <div className="form-group">
                        <label htmlFor="contact_message" className="required">Message </label>
                        <textarea onChange={handleFormChange} id="contact_message" name="message"
                                  required="required" className="form-control"></textarea>
                    </div>
                    <input style={styles.inputStyle} type="hidden" id="contact__token" name="contact[_token]"
                           value="M86jskjTXNGTaS4lSgDXmt_LiitYZSLIc_MY949TDRU"/>
                </div>
                <button style={styles.backButton} type="submit" className="btn btn-success g-recaptcha"
                        id="contact_action" data-sitekey="6LdpB3AUAAAAAGhw1sTJc0XCUNWKqbbD67b8IXMv"
                        data-callback="recaptcha_check_contact">
                    <i className="fa fa-envelope"></i>
                    Envoyer
                </button>
            </Form>
            {form.validation &&
                <div className="form-group">
                    <div className="alert alert-success" role="alert">
                        {form.validation}
                    </div>
                </div>
            }
        </React.Fragment>
    );
}