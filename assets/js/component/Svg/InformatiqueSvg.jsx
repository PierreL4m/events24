import React, {useState, useEffect, Component, useContext} from "react"
import {GlobalContext} from "../Context/GlobalContext.jsx";

export default function InformatiqueSvg (props){
    const value = useContext(GlobalContext);
    const DATE_OPTIONS = { year: 'numeric' };
    const styles = {
        colorPicto: {
            fill: value.place.colors[3].code,
        }
    };
    return (
        <div className={"sectoPictoBox"}>
            <div>
                <svg className={"svgSector"} version="1.1" id="icons" xmlns="http://www.w3.org/2000/svg" xmlnsXlink={"http://www.w3.org/1999/xlink"}  x="0px" y="0px" viewBox="0 0 288 288" xmlSpace={"preserve"}>
                    <g style={styles.colorPicto}>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path className="st0"
                                      d="M232.41,181.45h-7.17V89.55c0-4.85-3.95-8.79-8.8-8.79h-21.05v-7.17h21.05c8.81,0,15.97,7.16,15.97,15.97     V181.45z"/>
                            </g>
                            <g>
                                <path className="st0"
                                      d="M62.76,181.45h-7.17V89.55c0-8.8,7.16-15.97,15.96-15.97H203.5v7.17H71.55c-4.85,0-8.79,3.94-8.79,8.79     V181.45z"/>
                            </g>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <path className="st0"
                                  d="M233.11,214.41H54.89c-14.7,0-18.92-18.41-19.1-19.2c-0.23-1.06,0.03-2.17,0.71-3.02    c0.68-0.85,1.71-1.34,2.8-1.34h86.1c1.05,0,2.04,0.45,2.72,1.25l1.89,2.19h27.98l1.9-2.2c0.68-0.79,1.67-1.24,2.72-1.24h86.1    c1.09,0,2.12,0.49,2.8,1.34c0.68,0.85,0.94,1.96,0.71,3.02C252.03,196,247.8,214.41,233.11,214.41z M44.29,198.03    c1.71,3.97,5.02,9.21,10.6,9.21h178.21c5.58,0,8.89-5.24,10.6-9.21h-79.46l-1.9,2.2c-0.68,0.79-1.67,1.24-2.72,1.24h-31.26    c-1.05,0-2.04-0.45-2.72-1.25l-1.89-2.19H44.29z"/>
                        </g>
                    </g>
                </svg>
                <p className={"smallTextSectors"}>Informatique</p>
            </div>
        </div>
    );
}