import React, {useEffect, Component, useContext, useState, useCallback}  from "react"
import {Context} from "../Context/EventContext.jsx";

export function CheckIfState (idEvent) {
    const [state, setState] = useContext(Context);

    const fetchData = () => {
        fetch('/api/event/' + idEvent)
            .then(response => {
                return response.json()
            })
            .then(data => {
                setState(data)
            })
    }
    useEffect(() => {
        fetchData()
    }, [])
}

