import {BrowserRouter as Router, NavLink, Route} from "react-router-dom";
import {unmountComponentAtNode} from 'react-dom';
import React, {useEffect, Component, useContext} from "react";
import {Context} from "./Context/EventContext.jsx";
import LayoutElement from "./Layout.jsx";
import EventsElement from "./Events.jsx";
import Moment from 'react-moment';
import 'moment/locale/fr';
import moment from 'moment';
import { format } from 'date-fns'

function Carousel () {
    const [state, setState] = useContext(Context);
    return <React.Fragment>
                {
                    state.id == 0 ?
                        <EventsElement />
                    :
                        <LayoutElement />
                }
        </React.Fragment>
}


export default class CarouselElement extends Component {
    render(){
        return(<Carousel/>)
    }
    disconnectedCallback(){
        unmountComponentAtNode(this)
    }
}