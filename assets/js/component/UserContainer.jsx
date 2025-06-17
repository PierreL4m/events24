import React, {useState, useEffect, Component, useContext} from "react";
import AuthService from "./Services/auth.service";
import LoginButton from "./LoginButton.jsx";
import LogOutButton from "./LogOutButton.jsx";

export default function UserContainer(){
	const [user, setUser] = useState(null);
	const getUser = async () => {
		const   user  = await AuthService.getUser();
		setUser(user);
	}
	
	useEffect(() => {
    	getUser();
	}, []);
        
        return (
            <>
                <div className={"loginButtonContainer"}>
                    {user ? (
                        <>
                            <a href={"/check/secure_area"} id="loginButton">
                                <img className={"loginImg"} src={"/images/connection.svg"} />{user.email}
							</a>
                            <LogOutButton />
                        </>
                        )
						:
						(
                        <LoginButton />
                    )}
                </div>
            </>
        );
}