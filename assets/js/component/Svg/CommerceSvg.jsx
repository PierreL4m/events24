import React, {useState, useEffect, Component, useContext} from "react"
import {GlobalContext} from "../Context/GlobalContext.jsx";

export default function CommerceSvg (props){
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
                                      d="M157.99,82.79c-11.62,0-21.08-9.46-21.08-21.08c0-11.62,9.46-21.08,21.08-21.08     c11.62,0,21.07,9.46,21.07,21.08C179.06,73.33,169.61,82.79,157.99,82.79z M157.99,47.81c-7.67,0-13.91,6.24-13.91,13.91     s6.24,13.9,13.91,13.9c7.66,0,13.89-6.24,13.89-13.9S165.65,47.81,157.99,47.81z"/>
                            </g>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <path className="st0"
                                  d="M180.12,187.07v-7.17c12.46,0,14.32-12.3,14.32-19.64v-41.61c0-10.83-8.81-19.63-19.64-19.63h-33.63    c-10.83,0-19.63,8.81-19.63,19.63v34.15h-7.17v-34.15c0-14.78,12.03-26.81,26.81-26.81h33.63c14.78,0,26.81,12.03,26.81,26.81    v41.61C201.61,177.04,193.58,187.07,180.12,187.07z"/>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <path className="st0"
                                  d="M153.35,211.78H89.98c-1.98,0-3.59-1.61-3.59-3.59v-43.54c0-1.98,1.61-3.59,3.59-3.59h63.38    c1.98,0,3.59,1.61,3.59,3.59v43.54C156.94,210.18,155.33,211.78,153.35,211.78z M93.56,204.61h56.2v-36.36h-56.2V204.61z"/>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <rect x="177.5" y="125.67" className="st0" width="7.17" height="121.69"/>
                            </g>
                            <g>
                                <g>
                                    <rect x="131.3" y="125.67" className="st0" width="7.17" height="39.71"/>
                                </g>
                                <g>
                                    <rect x="131.3" y="208.44" className="st0" width="7.17" height="38.93"/>
                                </g>
                            </g>
                            <g>
                                <rect x="154.4" y="220.05" className="st0" width="7.17" height="27.32"/>
                            </g>
                        </g>
                    </g>
                </svg>
                <p className={"smallTextSectors"}>Commerce et relation client</p>
            </div>
        </div>
    );
}