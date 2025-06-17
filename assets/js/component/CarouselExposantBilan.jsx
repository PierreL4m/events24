import React, {useState, useEffect, Component, useContext} from "react"
import UniqueParticipationBilan from "./UniqueParticipationBilan.jsx";
import {Context} from "./Context/EventContext.jsx";
import {GlobalContext} from "./Context/GlobalContext.jsx";
import 'moment/locale/fr';
import axios from "axios";
import ModalPicturesBilan from "./ModalPicturesBilan";
import UseModal from "./Hooks/UseModal";
import { LazyLoadImage } from 'react-lazy-load-image-component';
import 'react-lazy-load-image-component/src/effects/opacity.css';
import Form from "react-validation/build/form";
import AuthService from "./Services/auth.service";
export default function CarouselExposantBilan (props) {
    const value = useContext(GlobalContext);
    const [state, setState] = useContext(Context);
    const [data, setData] = useState(null);
    const [filteredData, setFilteredData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [images, setImages] = useState([]);
    const [selectedImageCarousel, setSelectedImageCarousel] = useState(-1);
    const [isShowing, setIsShowing] = useState(false);
    useEffect(() => {
        axios.get('/api/participations/event/'+value.id)
            .then(function (response) {
                setImages([]);
                setData(response.data);
                response.data.map((participation) => {
                    participation.bilanPictures.map((image) => {
                        setImages((oldArray => [...oldArray, "/uploads/bilan_pictures/"+image.path]))
                    })
                })
            })
    }, []);
    const
        styles = {
            backEvent: {
                borderRadius:"25px"
            },
        }
    function filterPictures(e) {
        console.log(e.target)
        console.log(e.target.value)
        if(e.target.getAttribute("value") == ""){
            setFilteredData(null);
            setImages([]);
            const timeout = setTimeout(() => {
                data.map((participation) => {
                    participation.bilanPictures.map((image) => {
                        setImages((oldArray => [...oldArray, "/uploads/bilan_pictures/"+image.path]))
                    })
                })
            }, 500)
            return () => clearTimeout(timeout)
        }else{
            setFilteredData( data.filter(item => item.id == e.target.getAttribute("value")))
            setImages([]);
            const timeout = setTimeout(() => {
                data.filter(item => item.id == e.target.getAttribute("value")).map((participation) => {
                    participation.bilanPictures.map((image) => {
                        setImages((oldArray => [...oldArray, "/uploads/bilan_pictures/"+image.path]))
                    })
                })
            }, 500)
            return () => clearTimeout(timeout)
        }
    }

    function selectedimage(e, key){
        setSelectedImageCarousel(key)
        setIsShowing(true)
    }
    function toggleLoginForm(){
        setIsShowing(false)
    }
    return (
        <React.Fragment>
            {data &&
                <div>

                    <ul id={"slides"}>
                        {data &&

                            data.sort((a, b) => a.id - b.id).map((participation) => {
                                if (participation.premium == 1) {
                                    return (
                                        <button className={"uniqueParticiationBilan"} onClick={filterPictures} value={participation.id}>
                                            <div value={participation.id} className={"fakeBoxOrga"}>
                                                <div value={participation.id} className={"fakeBoxParticipation"}>
                                                    <div value={participation.id} className={"cardCarousselParticipation"}>
                                                        <li value={participation.id} style={participation.premium == true ? {
                                                            borderColor: value.place.colors[0].code,
                                                            borderStyle: 'solid',
                                                            borderWidth: '2px'
                                                        } : {
                                                            borderColor: 'lightgray',
                                                            borderStyle: 'solid',
                                                            borderWidth: '1px'
                                                        }}
                                                            className="slide p-2">
                                                            <p value={participation.id} className={"organizationName"}>{participation.companyName}</p>
                                                            {participation.logo &&
                                                                <div value={participation.id} className={"logoBox"}>
                                                                    <img value={participation.id} src={"/uploads/" + participation.logo.path}
                                                                         alt=""/>
                                                                </div>
                                                            }
                                                            <p value={participation.id} style={styles.infosExposants}
                                                               className={"infosExposants"}><img
                                                                className={"pictoExpo"}
                                                                src={"/images/dot.svg"}/>{participation.city}</p>
                                                            {participation.sector !== null ?
                                                                <p value={participation.id} style={styles.infosExposants}
                                                                   className={"infosExposants"}><img
                                                                    className={"pictoExpo"}
                                                                    src={"/images/heart.svg"}/>{participation.sector.name}
                                                                </p>
                                                                :
                                                                <p value={participation.id} style={styles.infosExposants}
                                                                   className={"infosExposants"}><img
                                                                    className={"pictoExpo"} src={"/images/heart.svg"}/>Divers
                                                                </p>
                                                            }
                                                            <div value={participation.id} style={styles.backButton}
                                                                 className={"buttonExposantOffer"}
                                                                 to={{pathname: "/Exposant/" + participation.id}}>
                                                                Voir les photos
                                                            </div>
                                                        </li>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                )
                                }
                            })
                        }
                        {data &&
                            data.sort((a, b) => a.id - b.id).map((participation) => {
                                if (participation.premium == 0 || participation.premium == null) {
                                    return (
                                        <button style={{outline:"none"}} className={"uniqueParticiationBilan"} onClick={filterPictures}
                                                value={participation.id}>
                                            <div value={participation.id} className={"fakeBoxOrga"}>
                                                <div value={participation.id} className={"fakeBoxParticipation"}>
                                                    <div  value={participation.id} className={"cardCarousselParticipation"}>
                                                        <li value={participation.id} style={participation.premium == true ? {
                                                            borderColor: value.place.colors[0].code,
                                                            borderStyle: 'solid',
                                                            borderWidth: '2px'
                                                        } : {
                                                            borderColor: 'lightgray',
                                                            borderStyle: 'solid',
                                                            borderWidth: '1px'
                                                        }}
                                                            className="slide p-2">
                                                            <p value={participation.id} className={"organizationName"}>{participation.companyName}</p>
                                                            {participation.logo &&
                                                                <div value={participation.id} className={"logoBox"}>
                                                                    <img value={participation.id} src={"/uploads/" + participation.logo.path}
                                                                         alt=""/>
                                                                </div>
                                                            }
                                                            <p value={participation.id} style={styles.infosExposants}
                                                               className={"infosExposants"}><img
                                                                className={"pictoExpo"}
                                                                src={"/images/dot.svg"}/>{participation.city}</p>
                                                            {participation.sector !== null ?
                                                                <p value={participation.id} style={styles.infosExposants}
                                                                   className={"infosExposants"}><img
                                                                    className={"pictoExpo"}
                                                                    src={"/images/heart.svg"}/>{participation.sector.name}
                                                                </p>
                                                                :
                                                                <p value={participation.id} style={styles.infosExposants}
                                                                   className={"infosExposants"}><img
                                                                    className={"pictoExpo"} src={"/images/heart.svg"}/>Divers
                                                                </p>
                                                            }
                                                            <div value={participation.id} style={styles.backButton}
                                                                 className={"buttonExposantOffer"}
                                                                 to={{pathname: "/Exposant/" + participation.id}}>
                                                                Voir les photos
                                                            </div>
                                                        </li>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    )
                                }
                            })
                        }
                    </ul>
                    {/*<select onChange={filterPictures} name="sector" id="sector-select" style={styles.selectors}>*/}
                    {/*    <option value="">Tous</option>*/}
                    {/*    {*/}
                    {/*        data.map((participation) => {*/}
                    {/*            return (*/}
                    {/*                <option key={participation.companyName}*/}
                    {/*                        value={participation.id}>{participation.companyName}</option>*/}
                    {/*            )*/}
                    {/*        })*/}
                    {/*    }*/}
                    {/*</select>*/}
                    <button className={"filteredImagesButton"} value={""} onClick={filterPictures}>Afficher toutes les photos</button>
                    <ul className={"galleryContainer"}>
                        {filteredData ?
                            images.map((image, key) => {
                                    return (
                                        <LazyLoadImage
                                            key={key}
                                            style={{minWidth:"250px",heigth:"365px;"}}
                                            wrapperClassName={"galleryWrapper"}
                                            image={image}
                                            onClick={(e) => selectedimage(e, key)}
                                            threshold={200}
                                            delayTime={5}
                                            effect="opacity"
                                            wrapperProps={{
                                                // If you need to, you can tweak the effect transition using the wrapper style.
                                                style: {transitionDelay: "1s"},
                                            }}
                                            src={image}
                                        />
                                    )
                            })
                            :
                            images.map((image, key) => {
                                    return (
                                        <LazyLoadImage
                                            key={image.key}
                                            style={{minWidth:"250px",heigth:"365px;"}}
                                            wrapperClassName={"galleryWrapper"}
                                            image={image}
                                            onClick={(e) => selectedimage(e, key)}
                                            effect="opacity"
                                            threshold={200}
                                            delayTime={5}
                                            wrapperProps={{
                                                // If you need to, you can tweak the effect transition using the wrapper style.
                                                style: {transitionDelay: "1s"},
                                            }}
                                            src={image}
                                        />
                                    )
                                })
                        }
                        <li className={"lastGallery"}></li>
                    </ul>
                </div>
            }
            {selectedImageCarousel > -1 &&
                <ModalPicturesBilan isShowing={isShowing} hide={toggleLoginForm} title="Les Photos" pictures={images} keyImg={selectedImageCarousel}/>
            }
</React.Fragment>
)
    ;
}