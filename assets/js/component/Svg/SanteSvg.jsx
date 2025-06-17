import React, {useState, useEffect, Component, useContext} from "react"
import {GlobalContext} from "../Context/GlobalContext.jsx";

export default function SanteSvg (props){
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
                            <path className="st0"
                                  d="M287.71,237.43h-10.08c-1.98,0-3.59-1.61-3.59-3.59s1.61-3.59,3.59-3.59h10.08c1.98,0,3.59,1.61,3.59,3.59    S289.69,237.43,287.71,237.43z"/>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <path className="st0"
                                  d="M264.19,237.43H42.29c-1.98,0-3.59-1.61-3.59-3.59s1.61-3.59,3.59-3.59h221.9c1.98,0,3.59,1.61,3.59,3.59    S266.17,237.43,264.19,237.43z"/>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <g>
                                    <g>
                                        <path className="st0"
                                              d="M61.68,221.52h-7.17v-85.64c0-1.98,1.61-3.59,3.59-3.59h23.52v7.17H61.68V221.52z"/>
                                    </g>
                                    <g>
                                        <g>
                                            <rect x="73.1" y="185.36" className="st0" width="7.17" height="15.87"/>
                                        </g>
                                        <g>
                                            <rect x="73.1" y="154.2" className="st0" width="7.17" height="15.87"/>
                                        </g>
                                        <g>
                                            <rect x="73.1" y="215.72" className="st0" width="7.17" height="17.47"/>
                                        </g>
                                    </g>
                                </g>
                            </g>
                            <g>
                                <g>
                                    <g>
                                        <path className="st0"
                                              d="M251.45,232.12h-7.17v-92.64h-19.93v-7.17h23.52c1.98,0,3.59,1.61,3.59,3.59V232.12z"/>
                                    </g>
                                    <g>
                                        <g>
                                            <rect x="225.68" y="185.36" className="st0" width="7.17" height="15.87"/>
                                        </g>
                                        <g>
                                            <rect x="225.68" y="154.2" className="st0" width="7.17" height="15.87"/>
                                        </g>
                                        <g>
                                            <rect x="225.68" y="215.72" className="st0" width="7.17" height="17.47"/>
                                        </g>
                                    </g>
                                </g>
                            </g>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <rect x="176.52" y="109.67" className="st0" width="17.85" height="7.17"/>
                            </g>
                            <g>
                                <g>
                                    <rect x="111.58" y="128.62" className="st0" width="82.79" height="7.17"/>
                                </g>
                                <g>
                                    <rect x="111.58" y="109.67" className="st0" width="17.85" height="7.17"/>
                                </g>
                                <g>
                                    <rect x="111.58" y="185.47" className="st0" width="82.79" height="7.17"/>
                                </g>
                                <g>
                                    <rect x="111.58" y="166.52" className="st0" width="82.79" height="7.17"/>
                                </g>
                                <g>
                                    <rect x="111.58" y="147.57" className="st0" width="82.79" height="7.17"/>
                                </g>
                            </g>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <path className="st0"
                                      d="M214.96,233.32h-7.17V86.97h-13.27v-7.17h16.86c1.98,0,3.59,1.61,3.59,3.59V233.32z"/>
                            </g>
                            <g>
                                <path className="st0"
                                      d="M98.17,233.32H91V83.38c0-1.98,1.61-3.59,3.59-3.59h15.98v7.17H98.17V233.32z"/>
                            </g>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <rect x="169.58" y="207.23" className="st0" width="7.17" height="26.52"/>
                            </g>
                            <g>
                                <rect x="129.21" y="207.23" className="st0" width="7.17" height="26.52"/>
                            </g>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <rect x="149.39" y="216.53" className="st0" width="7.17" height="15.97"/>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <path className="st0"
                                  d="M166.63,116.45h-27.3V97.15h-19.29v-27.3h19.29V50.57h27.3v19.29h19.29v27.3h-19.29V116.45z M146.51,109.27    h12.95V89.98h19.29V77.03h-19.29V57.74h-12.95v19.29h-19.29v12.95h19.29V109.27z"/>
                        </g>
                        <g xmlns="http://www.w3.org/2000/svg">
                            <rect x="121.84" y="202.26" className="st0" width="62.27" height="7.17"/>
                        </g>
                    </g>
                </svg>
                <p className={"smallTextSectors"}>Santé</p>
            </div>
        </div>
    );
}