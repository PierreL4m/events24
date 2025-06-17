import React, {useEffect, useState} from "react"
import 'moment/locale/fr';
import UniqueEventParticipation from "./UniqueEventParticipation";

export default function MyEvents (props) {
    const [nextEvent, setNextEvent] = useState(null);
    useEffect(() => {
            console.log(props.event);
            let nextTimeData = [];
            const sortedData = props.event.sort((a, b) => new Date(a.event.date) - new Date(b.event.date))
            sortedData.forEach((element) => {
                if (new Date().setHours(0) <= new Date(element.event.date)) {
                    nextTimeData.push(element)
                }
            })
            setNextEvent(nextTimeData);
    }, [props.event]);
    console.log(nextEvent)
    return (
        <>
            <p className={"categoryProfil"} style={{fontSize:"10px",marginBottom:"60px",marginTop:"15px"}}>Salons auxquels je suis inscrit :</p>
            {nextEvent &&
                nextEvent.map((participation, index) => {
                    return(
                        <UniqueEventParticipation user={props.user} event={participation.event} />
                    )
                })
            }
        </>
    )
}