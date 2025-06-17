import React, {useContext, useEffect, useState} from "react"
import {Link, useHistory} from "react-router-dom";
import UniqueParticipation from "./UniqueParticipation.jsx";
import 'moment/locale/fr';
import {GlobalContext} from "./Context/GlobalContext";
import AuthService from "./Services/auth.service";

export default function CheckSubscribe (props) {
    const value = useContext(GlobalContext);
    const history = useHistory();
    const [user, setUser] = useState(null);
    const getUser = async () => {
        const   user  = await AuthService.getUser();
        setUser(user);
    }
    useEffect(() => {
        getUser();
    }, []);
    console.log(user);
    return(
        <React.Fragment>
            {user &&
                <>
                    <Link onClick={history.goBack} className={"backToList"} to={"#"}>
                        Revenir à la page précédente
                    </Link>
                    <p id={"validationSubscribeMessage"}>Inscription validée, un mail vous a été envoyé à l'adresse <span id={"validationSubscribeMail"}>{user.email}</span>.</p>
                </>
            }
        </React.Fragment>
    )
}