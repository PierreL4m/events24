import React, {useEffect, useState} from "react";
import FileBase64 from 'react-file-base64';
import 'moment/locale/fr';
import AuthService from "./Services/auth.service";
import axios from "axios";

function FormField(props) {
    const [fileNameSelected, setFileNameSelected] = useState(null);
    const [findUser, setFindUSer] = useState(null);
    const [isShow, setIsShow] = useState(false);
    const [already, setAlready] = useState(false);
    const {
        label,
        type = 'text',
        name,
        value,
        errors,
        onChangeCvFile
    } = props;
    if(name == 'cv_file'){
        var isFile = true;
    }
    if(name == 'origin'){
        var isSelect = true;
    }
    if(errors[name] !== undefined && errors[name].errors !== undefined){
        var error = errors[name].errors[0];
    }
    function onChangeCv(e) {
        var fileName = '';
        if( e && e.length > 1 )
            fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
        else
            fileName = e[0].name.split( "\\" ).pop();

        if( fileName )
            onChangeCvFile;
            setFileNameSelected(fileName);
    }
    function togglePassword() {
        if(isShow === true){
            setIsShow(false)
        }else{
            setIsShow(true)
        }

    }
    const handleOnBlur = (e) => {
        axios.get('/api/userByMail/' + e.target.value)
            .then(function (response) {
                console.log(response.data)
                if(response.data != null){
                    setAlready(true);
                }else{
                    setAlready(false);
                }
            })
    }
    return (
        <>
                {isFile ?
                    <>
                        <div className="FormInput">
                            <div className={"uploadContainer"}>
                                <FileBase64
                                    multiple={ true }
                                    onDone={onChangeCvFile}
                                    defaultValue={props.saveData && props.saveData}
                                />
                                <label className={"labelFile"}>{props.fileName || props.saveData ? props.fileName : "Glissez votre CV ou cliquez pour ouvrir l'éxplorateur de fichier"}</label>
                                {error !== undefined &&
                                    <div className="errorSubscribe" role="alert">
                                        {error}
                                    </div>
                                }
                            </div>
                        </div>
                    </>
                    :name != "origin" && name != "working" &&
                    <>
                        <div className={"FormInput "+type+"Field"}>
                                {type == "checkbox" ?
                                    <>
                                        <div>
                                            <input
                                                className={"form-control"}
                                                type={type}
                                                name={name}
                                                value={
                                                    type == "checkbox" ?
                                                        1
                                                        : value
                                                }
                                            />
                                        </div>
                                        <label>{label}</label>
                                    </>
                                :type == "password" ?
                                    <>
                                        <div>
                                            <label>Mot de passe ( 8 caractères minimum, 1 chiffre, 1 majuscule ) </label>
                                            <input
                                                data-for="my-tooltip-2"
                                                className={"form-control"}
                                                type={isShow === true ? "text" : "password"}
                                                name={name}
                                            />
                                            <div className={"pictoEyeContainer"}>
                                                <img onMouseDown={togglePassword} className={"pictoExpo pictoShowPassword"} src={"/images/eye-solid.svg"} />
                                            </div>
                                        </div>
                                    </>
                                    :name =="email" ?
                                            <>
                                                <div>
                                                    <label>Email</label>
                                                    <input
                                                        data-for="my-tooltip-2"
                                                        className={"form-control"}
                                                        type={"email"}
                                                        name={name}
                                                        onBlur={handleOnBlur}
                                                    />
                                                    {already === true && <p style={{color:"red"}}>Vous avez déjà un compte, inscrivez vous à l'événement en vous connectant <a href={"/auth/login"}>ici</a></p>}
                                                </div>
                                            </>
                                    :
                                        <>
                                            <div>
                                                <label>{label}</label>
                                                <input
                                                    className={"form-control"}
                                                    type={type}
                                                    name={name}
                                                    defaultValue={props.saveData && props.saveData}
                                                />
                                            </div>
                                        </>
                                }
                            {error !== undefined &&
                            <div className="errorSubscribe" role="alert">
                                {error}
                            </div>
                            }
                        </div>
                    </>

                }
        </>
    )
}

export default FormField;