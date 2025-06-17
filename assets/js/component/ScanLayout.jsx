import React, {useContext, useEffect, useState} from "react"
import {Link, useHistory, useLocation} from "react-router-dom";
import 'moment/locale/fr';
import NavScan from "./NavScan";

export default function ScanLayout () {
    return(
        <React.Fragment>
            <NavScan />
        </React.Fragment>
    )
}