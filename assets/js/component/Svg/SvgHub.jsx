import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import ReactHtmlParser from 'react-html-parser';
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'
import AgricultureSvg from "./AgricultureSvg.jsx";
import CommerceSvg from "./CommerceSvg.jsx";
import IndustriSvg from "./IndustriSvg.jsx";
import TransportSvg from "./TransportSvg.jsx";
import ComptaSvg from "./ComptaSvg.jsx";
import HotelSvg from "./HotelSvg.jsx";
import InformatiqueSvg from "./InformatiqueSvg.jsx";
import ServiceSvg from "./ServiceSvg.jsx";
import SanteSvg from "./SanteSvg.jsx";
import SecuriteSvg from "./SecuriteSvg.jsx";
import FonctionSvg from "./FonctionSvg.jsx";
import DistributionSvg from "./DistributionSvg.jsx";

export default function SvgHub (props) {
    var svgName = props.svgName;
    return (
        <div>
            {(() => {
                switch (svgName) {
                    case 'Hotel':
                        return (
                            <HotelSvg />
                        );
                        break;
                    case 'Agriculture':
                        return (
                            <AgricultureSvg />
                        );
                        break;
                    case 'Commerce':
                        return (
                            <CommerceSvg />
                        );
                        break;
                    case 'Industri':
                        return (
                            <IndustriSvg />
                        );
                        break;
                    case 'Transport':
                        return (
                            <TransportSvg />
                        );
                        break;
                    case 'Compta':
                        return (
                            <ComptaSvg />
                        );
                        break;
                    case 'Informatique':
                        return (
                            <InformatiqueSvg />
                        );
                        break;
                    case 'Service':
                        return (
                            <ServiceSvg />
                        );
                        break;
                    case 'Sante':
                        return (
                            <SanteSvg />
                        );
                        break;
                    case 'Securite':
                        return (
                            <SecuriteSvg />
                        );
                        break;
                    case 'Fonction':
                        return (
                            <FonctionSvg />
                        );
                        break;
                    case 'Distribution':
                        return (
                            <DistributionSvg />
                        );
                        break;
                    default:
                        return null;
                }
            })()}
        </div>
    )


}