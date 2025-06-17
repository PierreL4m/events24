import React from "react"
import 'moment/locale/fr';
import UniqueEventParticipation from "./UniqueEventParticipation";
import UniqueNextEvent from "./UniqueNextEvent";

export default function MyNextEvent (props) {
    return (
        <>
            <p className={"categoryProfil"} style={{fontSize:"10px",marginBottom:"60px",marginTop:"15px"}}>Prochain événement :</p>
            <UniqueNextEvent user={props.user} event={props.event.event} />
        </>
    )
}