import React, {useState, useEffect, Component, useContext} from "react";
import {render, unmountComponentAtNode} from 'react-dom';
import {HashRouter as Router, Link, Route} from "react-router-dom";
import {Context, EventContext} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";

export default function SocialContainer (props) {
    const participation = props.participation;
    const value = useContext(GlobalContext);
    const styles = {
        backButton: {
            backgroundColor: value.place.colors[0].code,
        },
    };
    return (
        <React.Fragment>
            {participation.sites != null &&
                <a style={styles.backButton} className={"buttonExposant"} href={participation.sites[0].url} target={"_blank"}>
                    Voir le site
                </a>
            }
            <div className={"socialContainer"}>
                {participation.facebook != null &&
                <a className={"socialButton"} href={participation.facebook} target="_blank" >
                    <i className="fa-brands fa-facebook-f"></i>
                </a>
                }
                {participation.linkedin != null &&
                <a className={"socialButton"} href={participation.linkedin} target="_blank" ><i
                    className="fa-brands fa-linkedin-in"></i>
                </a>
                }
                {participation.instagram != null &&
                <a className={"socialButton"} href={participation.instagram} target="_blank" ><i
                    className="fa-brands fa-instagram"></i>
                </a>
                }
                {participation.twitter != null &&
                <a className={"socialButton"} href={participation.twitter} target="_blank" >
                    <i className="fa-brands fa-x-twitter"></i>
                </a>
                }
            </div>
            <hr style={styles.backButton}/>
        </React.Fragment>
    );
}