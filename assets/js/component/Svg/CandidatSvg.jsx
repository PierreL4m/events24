import React, {useState, useEffect, Component, useContext} from "react"
import {GlobalContext} from "../Context/GlobalContext.jsx";

export default function CandidatSvg (props){
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
                              d="M161.61,46.85c0,10.75-8.72,19.47-19.48,19.47c-10.75,0-19.47-8.72-19.47-19.47    c0-10.76,8.72-19.47,19.47-19.47C152.9,27.38,161.61,36.1,161.61,46.85z"/>
                        <path className="st0"
                              d="M170.8,77.57h-7.85l-12.96,37.56l-4.51-37.98l-5.16,0.06l-2.03,37.93l-13.46-37.57h-7.64    c-10.2,0-18.49,8.62-18.48,19.46l8.58,61.46c0,6.36,3,11.99,7.6,15.49l9.32,86.64h16.7v-85.47l6.78-0.17v85.64h16.7l9.38-87.14    c4.22-3.5,6.92-8.83,6.92-14.74l8.6-61.96C189.29,86.19,181,77.57,170.8,77.57z"/>
                    </g>
                    <path xmlns="http://www.w3.org/2000/svg" className="st0"
                          d="M82.82,79.43c0,8.13-6.59,14.71-14.72,14.71c-8.13,0-14.71-6.58-14.71-14.71c0-8.13,6.58-14.71,14.71-14.71   C76.23,64.72,82.82,71.31,82.82,79.43z"/>
                    <g xmlns="http://www.w3.org/2000/svg">
                        <path className="st0"
                              d="M231.81,79.43c0,8.13-6.59,14.71-14.72,14.71c-8.13,0-14.71-6.58-14.71-14.71c0-8.13,6.58-14.71,14.71-14.71    C225.22,64.72,231.81,71.31,231.81,79.43z"/>
                        <g>
                            <path className="st0"
                                  d="M89.76,102.65h-5.94l-9.79,28.39l-3.41-28.7l-3.9,0.05l-1.53,28.66l-10.18-28.4h-5.77     c-7.7,0-13.97,6.51-13.96,14.71l6.48,46.45c0,4.8,2.27,9.06,5.74,11.7l7.05,65.48h12.62v-64.6l5.13-0.13v64.73h12.62l7.09-65.86     c3.18-2.65,5.23-6.67,5.22-11.14l1.19-8.55l-7.35-52.66C90.63,102.75,90.21,102.65,89.76,102.65z"/>
                            <path className="st0"
                                  d="M238.75,102.65h-5.94l-9.79,28.39l-3.41-28.7l-3.9,0.05l-1.53,28.66l-10.17-28.4h-5.78     c-0.46,0-0.89,0.1-1.33,0.14l-7.31,52.68l1.16,8.33c0,4.8,2.27,9.06,5.74,11.7l7.05,65.48h12.61v-64.6l5.13-0.13v64.73h12.62     l7.09-65.86c3.18-2.65,5.23-6.67,5.23-11.14l6.5-46.83C252.72,109.16,246.45,102.65,238.75,102.65z"/>
                        </g>
                    </g>

                </g>
            </svg>
                </div>
                <div className={"keywordTextContainer"}>
                <p className={"bigTextKeyNumber"}>+{props.number}</p>
                <p className={"smallTextKeyNumber"}>candidats*</p>
                </div>
            </div>
        </div>
    );
}