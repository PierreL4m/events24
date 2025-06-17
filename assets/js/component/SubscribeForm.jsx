import React, {useState, useEffect, Component, useContext} from "react";
import Form from "react-validation/build/form";
import Input from "react-validation/build/input";
import CheckButton from "react-validation/build/button";
import FormField from "./FormField.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import {Context} from "./Context/EventContext.jsx";
import AuthService from "./Services/auth.service.js";
import LoaderElement from "./Loader";
import ReactPixel from 'react-facebook-pixel';
import {Link, Routes, Route, useHistory} from 'react-router-dom';
import axios from "axios";

const required = value => {
    if (!value) {
        return (
            <div className="alert alert-danger" role="alert">
                Ce champ est obligatoire !
            </div>
        );
    }
};

export default function SubscribeForm () {
    const history = useHistory();
    const [fileName, setFileName] = useState(null);
    const [loadingSubscribe, setloadingSubscirbe] = useState(true);
    const [form, setForm] = useState({
        lastname: "",
        firstname: "",
        email: "",
        phone: "",
        plainPassword: "",
        cv_file: "",
        mailingEvents: false,
        mailingRecall: false,
        phoneRecall: false,
        loading: false,
        message: "",
        validation:""
    });
    const [errors, setErrors] = useState({
        errors:[]
    });
    const user = AuthService.getUser();
    const value = useContext(GlobalContext);
    const [state, setState] = useContext(Context);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [name, setName] = useState(null);
    const [firstname, setFirstname] = useState(null);
    const [email, setEmail] = useState(null);
    const [phone, setPhone] = useState(null);
    const [cv, setCv] = useState(null);
    const [password, setPassword] = useState(null);
    const getData = async () => {
        const { data } = await AuthService
            .get('/api/event/'+value.id+'/form')
            .then(res => {
                return res;
            });
        setloadingSubscirbe(false);
        setData(data);
    };

    useEffect(() => {
        getData();
    }, []);
    const handleSubscribe = (e) => {
        setloadingSubscirbe(true);
        e.preventDefault();
        Form.form.validateAll();
        setName(e.target.lastname.value);
        setFirstname(e.target.firstname.value);
        setEmail(e.target.email.value);
        setPhone(e.target.phone.value);
        setCv(form.cv_file);
        setPhone(e.target.phone.value);
        AuthService.register(
            value.id,
            e.target.firstname.value,
            e.target.lastname.value,
            e.target.email.value,
            e.target.phone.value,
            e.target.plainPassword.value,
            form.cv_file,
            e.target.mailingEvents.checked ? 1 : 0,
            e.target.mailingRecall.checked ? 1 : 0,
            e.target.phoneRecall.checked ? 1 : 0,
        ).then(
            (response) => {
                if (response.status == 201) {
                    AuthService.login(
                        e.target.email.value,
                        e.target.plainPassword.value
                    ).then(
                        (response) => {
                            history.push("/check_subscribe_validation");
                        }
                    );
                }else{
                    setloadingSubscirbe(false);
                    handleErrors(response);
                }
            }
        );
    };
    const handleCvChange = (file) => {
        const updatedForm = {
            ...form,
            cv_file: file[0].base64
        };
        setForm(updatedForm);
        setFileName(file[0].file.name);
    }

    const handleErrors = (errors) => {
        setErrors(errors);
    };

    const styles = {
        backEvent: {
            backgroundColor: "#787878",
            color: "white",
            borderRadius:"25px"
        }
    }
    return (
        <>
            {loadingSubscribe === true &&
                <LoaderElement />
            }
            {loadingSubscribe === false &&
                <div>
                    <div className="card-container">
                        <Form
                            className={"form"}
                            onSubmit={handleSubscribe}
                            ref={c => {
                                Form.form = c;
                            }}
                        >
                            <>
                                {data &&
                                 <FormField saveData = {data.find(formName => formName.name === "email").name == "email" && email }message={form.message} key={data.find(formName => formName.name === "email").name} required={required} label={data.find(formName => formName.name === "email").label} type={data.find(formName => formName.name === "email").fieldType} name={data.find(formName => formName.name === "email").name} value={data.find(formName => formName.name === "email").emailAddress} fileName={fileName} onChangeCvFile={handleCvChange} errors={errors}/>
                                }
                                {data &&
                                    data.filter(formName => formName.name !== "email").map((form) => {
                                        var formName = form.name;
                                        return (
                                            <FormField saveData = {form.name == "lastname" ? name : form.name == "firstname" ? firstname : form.name == "phone" ? phone : form.name == "cv_file" ? cv : form.name == "password" && password }message={form.message} key={form.name} required={required} label={form.label} type={form.fieldType} name={form.name} value={form.emailAddress} fileName={fileName} onChangeCvFile={handleCvChange} errors={errors}/>
                                        )
                                    })
                                }

                            </>
                            <div className="form-group">
                                {data &&
                                    <button style={styles.backEvent} className="btn btn-primary btn-block">
                                        {loadingSubscribe === true ?
                                            <span>Chargement ...</span>
                                            :
                                            <span>Valider l'inscription</span>
                                        }
                                    </button>
                                }
                            </div>
                            {/*{form.message && (*/}
                            {/*    <div className="form-group">*/}
                            {/*        <div className="alert alert-danger" role="alert">*/}
                            {/*            {form.message}*/}
                            {/*        </div>*/}
                            {/*    </div>*/}
                            {/*)}*/}
                            {form.validation && (
                                <div className="form-group">
                                    <div className="alert alert-success" role="alert">
                                        {form.validation}
                                    </div>
                                </div>
                            )
                            }
                            <CheckButton
                                style={{ display: "none" }}
                                ref={c => {
                                    CheckButton.checkBtn = c;
                                }}
                            />
                        </Form>
                    </div>
                </div>

            }
        </>
    );
}

