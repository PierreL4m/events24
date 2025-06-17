import React, {Component, useState} from "react";
import { BrowserRouter, Switch, Route, Redirect, Link } from "react-router-dom";
import Form from "react-validation/build/form";
import Input from "react-validation/build/input";
import CheckButton from "react-validation/build/button";
import AuthService from "./Services/auth.service.js";
const required = value => {
    if (!value) {
        return (
            <div className="alert alert-danger" role="alert">
                Ce champ est obligatoire !
            </div>
        );
    }
};
export default class Login extends Component {
    constructor(props) {
        super(props);
        this.handleLogin = this.handleLogin.bind(this);
        this.onChangeUsername = this.onChangeUsername.bind(this);
        this.onChangePassword = this.onChangePassword.bind(this);
        this.redirectTo = this.redirectTo.bind(this);
        this.togglePassword = this.togglePassword.bind(this);
        this.state = {
            username: "",
            password: "",
            loading: false,
            message: "",
            validation:"",
            redirect:false,
            isShown:false
        };
    }
    redirectTo() {
        if(window.location.pathname == "/auth/login"){
            window.location.href="/check/secure_area";
        }else{
            window.location.href="/check/secure_area";
        }
        return null;
    }
    togglePassword() {
        if(this.state.isShown === false){
            this.setState({
                isShown: true
            })
        }else{
            this.setState({
                isShown: false
            })
        }
    }
    onChangeUsername(e) {
        this.setState({
            username: e.target.value
        });
    }
    onChangePassword(e) {
        this.setState({
            password: e.target.value
        });
    }
    handleLogin(e) {
        e.preventDefault();
        this.setState({
            loading: true
        });
        this.form.validateAll();
        if (this.checkBtn.context._errors.length === 0) {
			AuthService.login(this.state.username, this.state.password).then(
                () => {
				this.setState({
                    validation: "Connexion réussie !",
                    redirect:true
                });
                }
            )
            .catch((e) => {
				this.setState({
                    loading: false,
                    message: "Identfiants Incorrects"
                });
			});
        } else {
            this.setState({
                loading: false,
                message: "Identfiants Incorrects"
            });
        }
    }
    render() {
        if (this.state.redirect) {
			return this.redirectTo();
		}
        return (
            <div className="col-md-12">
                <div className="card-container">
                    <Form
                        onSubmit={this.handleLogin}
                        ref={c => {
                            this.form = c;
                        }}                    >
                        <div className="form-group">
                            <label htmlFor="username">Adresse Mail / Identifiant</label>
                            <Input
                                type="text"
                                className="form-control"
                                name="username"
                                value={this.state.username}
                                onChange={this.onChangeUsername}
                                validations={[required]}
                            />
                        </div>
                        <div className="form-group">
                            <label htmlFor="password">Mot de Passe</label>
                            <Input
                                type={this.state.isShown === false ? "password" : "text"}
                                className="form-control"
                                name="password"
                                value={this.state.password}
                                onChange={this.onChangePassword}
                                validations={[required]}
                            />
                            <img onMouseDown={this.togglePassword} className={"pictoExpo pictoShowPasswordSub"} src={"/images/eye-solid.svg"} />
                        </div>
                        <div className="form-group">
                            <button
                                className="btn btn-primary btn-block"
                                disabled={this.state.loading}
                            >
                                {this.state.loading && (
                                    <span className="spinner-border spinner-border-sm"></span>
                                )}
                                <span>Se connecter</span>
                            </button>
                        </div>
                        {this.state.message && (
                            <div className="form-group">
                                <div className="alert alert-danger" role="alert">
                                    {this.state.message}
                                </div>
                            </div>
                        )}
                        {this.state.validation && (
                        <div className="form-group">
                            <div className="alert alert-success" role="alert">
                                {this.state.validation}
                            </div>
                        </div>
                        )}
                        <CheckButton
                            style={{ display: "none" }}
                            ref={c => {
                                this.checkBtn = c;
                            }}
                        />
                        <a className={"forgotPassword"} href={"/reset-password/request"}>Mot de passe oublié ?</a>
                    </Form>
                </div>
            </div>
        );
    }
}
