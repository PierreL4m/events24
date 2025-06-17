import React, {useContext, useState} from "react";
import {Context, EventContext} from "./Context/EventContext.jsx";
import 'moment/locale/fr';
import AuthService from "./Services/auth.service";
import {GlobalContext} from "./Context/GlobalContext";
import FileBase64 from 'react-file-base64';
import LoaderElement from "./Loader";

export default function CvField (props) {
    const [fileName, setFileName] = useState(null);
    const [loadingSubscribe, setloadingSubscirbe] = useState(false);
    const [form, setForm] = useState({
        lastname: "",
        firstname: "",
        email: "",
        phone: "",
        plainPassword: "",
        cv_file: "",
        mailingEvents: false,
        mailingRecall: false,
        phoneRecall: false,
        loading: false,
        message: "",
        validation:""
    });
    const [errors, setErrors] = useState({
        errors:[]
    });
    const user = AuthService.getUser();
    const value = useContext(GlobalContext);
    const [state, setState] = useContext(Context);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [name, setName] = useState(null);
    const [firstname, setFirstname] = useState(null);
    const [email, setEmail] = useState(null);
    const [phone, setPhone] = useState(null);
    const [cv, setCv] = useState(null);
    const [password, setPassword] = useState(null);
    const handleCvChange = (file) => {
        setloadingSubscirbe(true);
        AuthService.cvUpdate(
            file[0],
            props.user.id
        ).then(
            (response) => {
                if (response.status == 200) {
                    window.location.reload(true);
                }else{
                    setloadingSubscirbe(false);
                    handleErrors(response);
                }
            }
        );
        setFileName(file[0].file.name);
    }
    return (
        <React.Fragment>
            <div className="FormInput">
                <div className={"uploadContainerCv"}>
                    {loadingSubscribe ?
                        <LoaderElement />
                        :
                        <>
                            <FileBase64
                                multiple={ true }
                                onDone={handleCvChange}
                                defaultValue={props.saveData && props.saveData}
                            />
                            <label className={"labelFile"}>{fileName ? fileName : "Glissez votre CV ou cliquez pour ouvrir l'éxplorateur de fichier"}</label>
                            {error !== undefined &&
                                <div className="errorSubscribe" role="alert">
                                    {error}
                                </div>
                            }
                        </>
                    }
                </div>
            </div>
        </React.Fragment>
    );
}