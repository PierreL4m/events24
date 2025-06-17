import React, {useState, useEffect, Component, useContext} from "react"
import {GlobalContext} from "../Context/GlobalContext.jsx";

export default function MeetingSvg (props){
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
                            <path xmlns="http://www.w3.org/2000/svg" className="st0"
                                  d="M102.43,46.64c0,10.78-8.73,19.51-19.52,19.51c-10.77,0-19.51-8.73-19.51-19.51   c0-10.78,8.74-19.51,19.51-19.51C93.7,27.13,102.43,35.86,102.43,46.64z"/>
                            <path xmlns="http://www.w3.org/2000/svg" className="st0"
                                  d="M221.63,46.64c0,10.78-8.74,19.51-19.52,19.51c-10.77,0-19.5-8.73-19.5-19.51c0-10.78,8.73-19.51,19.5-19.51   C212.9,27.13,221.63,35.86,221.63,46.64z"/>
                            <path xmlns="http://www.w3.org/2000/svg" className="st0"
                                  d="M146.64,146.34c-5.16,2.89-11.06,3.52-15.25,3.52c-2.14,0-3.52-0.18-3.6-0.19c-1.73-0.27-3.41-0.76-5.29-1.53   c-3.67-1.51-6.6-3.81-8.71-6.85c-6.04-8.71-3.93-20.35-3.67-21.68c3.41-15.54,13.4-18.85,19.05-19.44   c0.83-0.09,1.57-0.11,2.21-0.11c5.81,0,10.46,2.12,11.49,2.64l0.98,0.59c0.87,0.63,1.7,0.95,2.45,0.95c0.07,0,0.13,0,0.19-0.01   c0.32-3.43-0.97-11.73-3.5-20.62l-1.12-5.59l-0.28-0.85c-10.02,2.27-21.1,0.85-26.2,0.92l-11.62-0.67l-12.98,37.65L86.26,77   l-5.18,0.07l-2.04,38.01L65.57,77.42h-7.66c-10.22,0-18.52,8.64-18.52,19.51l8.6,61.6c0,6.37,3.01,12.02,7.61,15.51l9.35,86.84   h16.73v-85.67l6.8-0.17v85.83h16.74l9.39-87.34c0.26-0.21,0.49-0.45,0.73-0.67c10.66-1.24,21.11-0.2,27.89,0.69   c0.2-0.71,0.38-1.41,0.55-2.1c0.02,0,0.06,0.01,0.09,0.01C146.61,160.68,146.69,149.83,146.64,146.34z"/>
                            <path xmlns="http://www.w3.org/2000/svg" className="st0"
                                  d="M230.08,77.42h-7.88l-12.97,37.65L204.7,77l-5.17,0.07l-1.88,34.98c-0.41-0.04-0.85-0.09-1.3-0.15   l-12.35-34.48l-0.27,0.05c-2.92,0.22-19.41,0.47-35.1-1.43c0,0,11.17,33.71,1.62,35.31c0,0-5.2,1.88-10.9-1.88   c0,0-17.34-8.49-21.76,11.68c0,0-3.72,18.57,11.14,20.96c0,0,9.29,1.06,15.39-3.18c0,0,8.76-4.78,10.09,5.57   c0,0,0.79,15.65-3.45,30.77c0,0,11.82,2.94,23.57,1.56l9.05,84.05h16.73v-85.67l6.8-0.17v85.83h16.73l9.4-87.34   c4.23-3.51,6.94-8.85,6.94-14.77l8.61-62.1C248.61,86.06,240.3,77.42,230.08,77.42z"/>
                        </g>
                    </svg>
                </div>
                <div className={"keywordTextContainer"}>
                    <p className={"bigTextKeyNumber"}>+{props.number}</p>
                    <p className={"smallTextKeyNumber"}>entretiens post-salons*</p>
                </div>
            </div>
        </div>
    );
}