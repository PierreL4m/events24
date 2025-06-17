import React, { useState } from "react";
import ReactDOM from "react-dom";
import Login from "./LoginForm.jsx"
import UniqueParticipation from "./UniqueParticipation";
import UniqueEventParticipation from "./UniqueEventParticipation";
import "react-responsive-carousel/lib/styles/carousel.min.css";
import { Carousel } from 'react-responsive-carousel';

const ModalPicturesBilan = ({setToken, isShowing, hide,pictures, keyImg }) =>
    isShowing
        ? ReactDOM.createPortal(
            <>
                <div className="modal-overlay">
                    <div className="modal-wrapper">
                        <div className="modal-globalBilan">
                            <div className={"divExitButton"}>
                                <button type="button" className="modal-close-button" onClick={hide}>
                                    x
                                </button>
                            </div>
                            <div className="modal-header">
                                <p>Les photos</p>
                            </div>
                            <Carousel selectedItem={keyImg}>
                                {pictures.map((picture, index) => {
                                    return (
                                                <div className={"selectedImgCarousel"} style={{backgroundImage: "url(" + picture + ")"}}>
                                                    <img src={picture} style={{width:"100%"}}/>
                                                </div>
                                    )
                                })}
                            </Carousel>
                        </div>
                    </div>
                </div>
            </>,
            document.body
        )
        : null;

export default ModalPicturesBilan;