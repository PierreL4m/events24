import React, { Component } from "react";
import { Switch, Route, Link } from "react-router-dom";
import AuthService from "./Services/auth.service";

export default class LogOutButton extends Component {
    constructor(props) {
        super(props);
        this.logOut = this.logOut.bind(this);
    }
    logOut() {
        AuthService.logout();
    }
    render(){
        return(
            <>
                    <a href="/" onClick={this.logOut} id="logOutButton">
                        <i className="fa fa-sign-out"></i>
                    </a>
            </>
        )
    }

}
