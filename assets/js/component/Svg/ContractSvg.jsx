import React, {useState, useEffect, Component, useContext} from "react"
import {GlobalContext} from "../Context/GlobalContext.jsx";

export default function ContractSvg (props){
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
                          d="M168.03,167.75c-2.45,2.45-5.39,4.22-8.59,5.27l0.03,0.03l-0.41,0.11c-0.11,0.03-0.22,0.06-0.33,0.09   l-22.77,6.1l-23.52,6.3c-3.32,0.89-6.37-2.15-5.48-5.48l6.3-23.52l6.3-23.52l0.05,0.05c1.05-3.2,2.82-6.14,5.27-8.59l76.3-76.3   c-2.95-1.55-6.29-2.44-9.83-2.44H87.87c-11.7,0-21.27,9.57-21.27,21.27v158.63c0,11.7,9.57,21.27,21.27,21.27h103.47   c11.7,0,21.27-9.57,21.27-21.27V123.17L168.03,167.75z"/>
                    <path xmlns="http://www.w3.org/2000/svg" className="st0"
                          d="M152.97,148.61l-10.53-10.53c-1.65-1.65-1.65-4.36,0-6.01l89.77-89.86c1.65-1.65,4.36-1.65,6.01,0l10.53,10.53   c1.65,1.65,1.65,4.36,0,6.01l-89.77,89.86C157.33,150.27,154.62,150.27,152.97,148.61z"/>
                    <path xmlns="http://www.w3.org/2000/svg" className="st0"
                          d="M139.07,167.98l-16.04,5.9c-2.39,0.58-4.54-1.57-3.96-3.96l5.16-16.78L139.07,167.98z"/>
                </g>
            </svg>
                </div>
                <div className={"keywordTextContainer"}>
                <p className={"smallTextKeyNumber"}>CDI, CDD, Intérim, Stage / Alternance, Formation</p>
                </div>
            </div>
        </div>
    );
}