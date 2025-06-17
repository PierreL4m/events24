import React, {useState, useEffect, Component, useContext} from "react"
import {BrowserRouter as Router, NavLink, Route} from "react-router-dom"
import {GlobalContext} from "./Context/GlobalContext.jsx";
import SvgHub from "./Svg/SvgHub.jsx";
import AgricultureSvg from "./Svg/AgricultureSvg.jsx";
import CommerceSvg from "./Svg/CommerceSvg.jsx";
import IndustriSvg from "./Svg/IndustriSvg.jsx";
import TransportSvg from "./Svg/TransportSvg.jsx";
import ComptaSvg from "./Svg/ComptaSvg.jsx";
import HotelSvg from "./Svg/HotelSvg.jsx";
import InformatiqueSvg from "./Svg/InformatiqueSvg.jsx";
import ServiceSvg from "./Svg/ServiceSvg.jsx";
import SanteSvg from "./Svg/SanteSvg.jsx";
import SecuriteSvg from "./Svg/SecuriteSvg.jsx";
import FonctionSvg from "./Svg/FonctionSvg.jsx";
import DistributionSvg from "./Svg/DistributionSvg.jsx";
import Data from "./Data/FakeData.json";
import AuthService from "./Services/auth.service.js";

export default function Sectors () {
    const value = useContext(GlobalContext);
    const [data, setData] = useState(null);
    const [pictoEvent, setPictoEvent] = useState(null);
    const styles = {
        title: {
            textAlign: 'center',
            marginTop:'55px',
            marginBottom:'30px',
            width:'100%',
            fontSize: '14px',
            textTransform: 'uppercase',
            fontXeight: '450',
            letterSpacing: '1px',
            color: 'grey'
        }
    };
    const getPictoEvent = async () => {
        const { data } = await AuthService
            .get('/api/pictoSector/'+value.id)
            .then(res => {
                return res;
            })
            .catch(e => {});
        setPictoEvent(data);
    };
    useEffect(() => {
        getPictoEvent();
    }, []);
        return (
            <React.Fragment>
                {value.type.id == 2 ?
                    pictoEvent &&
                    <div className={"pictoContainer"}>
                        <h2 style={styles.title}>Dans tous les secteurs</h2>
                        {pictoEvent.map((picto) => {
                            return(
                                <SvgHub svgName={picto.slug}/>
                            )
                        })}
                    </div>

                    :
                    <div className={"pictoContainer"}>
                        <h2 style={styles.title}>Dans tous les secteurs</h2>
                        <AgricultureSvg />
                        <CommerceSvg />
                        <IndustriSvg />
                        <TransportSvg />
                        <ComptaSvg />
                        <HotelSvg />
                        <InformatiqueSvg />
                        <ServiceSvg />
                        <SanteSvg />
                        <SecuriteSvg />
                        <FonctionSvg />
                        <DistributionSvg />
                    </div>


                }
            </React.Fragment>
        );
}