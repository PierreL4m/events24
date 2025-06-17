import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {GlobalContext} from "./Context/GlobalContext.jsx";

export default function Partners () {
    const value = useContext(GlobalContext);
    const partners = value.partners;
    const styles = {
        title: {
            textAlign: 'center',
            marginTop:'80px',
            marginBottom:'30px',
        }
    };
    return (
        <React.Fragment>
            <div className={"row"}>
                <div className={"col-12 partnersHeader"}>
                    <h2 style={styles.title}>Nos partenaires</h2>
                </div>
            </div>
            <div className={"allPartnersLogos"}>
                {partners &&
                    partners.map((partner, index) => {
                            return (
                                <a key={index} style={styles.backButton} className={"logoPartnerBox"} href={partner.url}>
                                    <img className={"logoPartner"} src={"uploads/"+partner.logo.path} alt=""/>
                                </a>
                            )
                    })
                }
            </div>
        </React.Fragment>
    );
}