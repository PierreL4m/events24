import React, {useState, useEffect, Component, useContext} from "react"
import {GlobalContext} from "../Context/GlobalContext.jsx";

export default function OfferSvg (props){
    const value = useContext(GlobalContext);
    const DATE_OPTIONS = { year: 'numeric' };
    const styles = {
        colorPicto: {
            fill: value.place.colors[4].code,
        },
        backPicto: {
            backgroundColor: value.place.colors[2].code,
        }
    };
    return (
        <div style={styles.backPicto} className={"keyNumberBox"}>
            <div className={"keyNumberContent"}>
                <div className={"keywordSvgContainer"}>
            <svg version="1.1" id="icons" xmlns="http://www.w3.org/2000/svg" xmlnsXlink={"http://www.w3.org/1999/xlink"}  x="0px" y="0px" viewBox="0 0 288 288" xmlSpace={"preserve"}>
                <g style={styles.colorPicto}>
                    <g xmlns="http://www.w3.org/2000/svg">
                        <path className="st0"
                              d="M242.33,217.4l-61.12-61.12c17.87-29.11,14.22-67.7-10.99-92.91c-29.51-29.51-77.35-29.51-106.86,0    c-29.51,29.51-29.51,77.35,0,106.85c25.21,25.22,63.8,28.87,92.91,10.99l61.12,61.12c5.9,5.9,15.47,5.9,21.37,0l3.56-3.56    C248.23,232.87,248.23,223.3,242.33,217.4z M161.32,161.32c-24.55,24.55-64.5,24.55-89.05,0c-24.55-24.55-24.55-64.5,0-89.05    c24.55-24.55,64.49-24.55,89.05,0C185.87,96.83,185.87,136.77,161.32,161.32z"/>
                    </g>
                    <path xmlns="http://www.w3.org/2000/svg" className="st0"
                          d="M131.3,89.27c0,8.8-7.14,15.94-15.94,15.94c-8.8,0-15.94-7.13-15.94-15.94c0-8.8,7.14-15.94,15.94-15.94   C124.17,73.33,131.3,80.47,131.3,89.27z"/>
                    <g xmlns="http://www.w3.org/2000/svg">
                        <path className="st0"
                              d="M116.97,163.98c13.59,0,25.81-5.84,34.4-15.09l2.6-18.74c0-8.67-6.79-15.72-15.13-15.72h-6.43l-10.61,30.74    l-3.7-31.09l-4.22,0.05l-1.66,31.05l-11.02-30.76l-6.25,0c-8.35,0-15.13,7.05-15.13,15.92l2.54,18.23    C90.93,158.01,103.25,163.98,116.97,163.98z"/>
                    </g>
                </g>
            </svg>
                </div>
                <div className={"keywordTextContainer"}>
                <p className={"bigTextKeyNumber"}>+{props.number}</p>
                <p className={"smallTextKeyNumber"}>offres à pourvoir</p>
                </div>
            </div>
        </div>
    );
}