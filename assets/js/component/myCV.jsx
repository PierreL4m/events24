import React, {useState} from "react"
import 'moment/locale/fr';
import UseModal from "./Hooks/UseModal";
import ModalCv from "./ModalCv";

export default function MyCv (props) {
    const [cv, setCv] = useState(props.user.cv);
    const file = '/uploads/cvs/'+props.user.cv
    const [type, setType] = useState(props.user.cv.split('.').pop());
    const { isShowing: isLoginFormShowed, toggle: toggleLoginForm } = UseModal();
    const {
        isShowing: isRegistrationFormShowed,
        toggle: toggleRegistrationForm
    } = UseModal();

    return (
        <>
            {cv ?
                <>
                    <p className={"categoryProfil"} style={{fontSize:"10px",marginBottom:"40px",marginTop:"40px"}}>Mon CV</p>
                    <div style={{height:'fit-content', position:"relative"}}>
                        {(type == "jpg" || "jpeg" || "png") &&
                            <div style={{width: "100%", display: "flex", justifyContent: "center"}}>
                                <img style={{width: "300px"}} src={"/uploads/cvs/" + props.user.cv} alt=""/>
                            </div>
                        }
                        {type == "pdf" &&
                            <div style={{display: "flex", justifyContent: "center", width: "100%"}}>
                                <object data={"/uploads/cvs/" + props.user.cv} type='application/pdf' width='300px'
                                        height='458px'></object>
                            </div>
                        }
                        {type == "docx" &&
                            <p style={{textAlign:"center"}}>Les fichier Word ne sont pas reconnus, veuillez télécharger le fichier pour vérifier ci celui-ci est le bon. </p>
                        }
                        <div className={"cvButtonContainer"}>
                            <button onClick={toggleLoginForm} className={"buttonManageCv"}><i
                                style={{color: "#575756", fontSize: "33px"}} className="fa-solid fa-edit"></i>
                            </button>
                            <p style={{margin: "0", fontSize: "30px"}}>|</p>
                            <a className={"buttonManageCv"} href={"/uploads/cvs/"+props.user.cv} target={"_blank"} download><i style={{color:"#575756",fontSize:"33px"}} className="fa-solid fa-download"></i></a>
                        </div>
                    </div>
                </>
                :
                <>
                    <button  onClick={toggleLoginForm} style={{backgroundColor:props.user.candidateParticipations[0].event.place.colors[0].code, marginTop:"50px"}} className={"uploadCvButton"}>Ajouter un CV</button>
                </>
            }
            <ModalCv user={props.user} isShowing={isLoginFormShowed} hide={toggleLoginForm} title="Login" />

        </>
    )
}