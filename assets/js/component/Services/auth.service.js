import axios from "axios";
import cookies from "js-cookies";
import UserContainer from "../UserContainer.jsx";

class AuthService {

	login(username, password) {
	 	return this
            .post("/auth/apilogin", {
                "username":username,
                "password":password
            })
            .then(response => {
				return this.getUser();
            })
            .catch(
				(e) => {
					throw e;
				}
			);
	}
	
	async requestUser() {
		return axios
            .get("/api/user/me")
            .then(res => {
				let u = res.data;
				if(u == null || u == 'null') {
					u = null;
				}
				return u;
            })
            .catch((e) => {
				throw e;
			});
	}
	
	async getUser() {
		let u = null;
		return await this.requestUser().then(res => {
			u = res;
			return u;
		})
		.catch((e) =>  {
			location.reload();
			return null;
		});
	}



    workingUpdate(working, user) {
        return this
            .patch("/api/candidate/working/update/"+user,
                {
                    "working":working
                }
            )
            .then(response => {
                return response;
            }).catch(function (error) {
                if (error.response) {
                    return error.response.data.errors.children;
                }
            });
    }

    smsUpdate(sms, user) {
        return this
            .patch("/api/candidate/sms/update/"+user,
                {
                    "sms":sms
                }
            )
            .then(response => {
                return response;
            }).catch(function (error) {
                if (error.response) {
                    return error.response.data.errors.children;
                }
            });
    }

    addAccred(participation, firstname, lastname, mail, phone) {
        return this
            .post("/api/accreditation/add/"+participation,
                {
                    "participation":participation,
                    "firstname":firstname,
                    "lastname":lastname,
                    "email":mail,
                    "phone":phone,
                }
            )
            .then(response => {
                return response;
            })
    }

    register(event, firstname, lastname, mail, phone, password, cv, mailingEvents, mailingRecall, phoneRecall) {
        return this
            .post("/api/event/"+event+"/registration",
                {
                    "firstname":firstname,
                    "lastname":lastname,
                    "email":mail,
                    "phone":phone,
                    "plainPassword":password,
                    "cv_file":cv,
                    "mailingEvents":mailingEvents,
                    "mailingRecall":mailingRecall,
                    "phoneRecall": phoneRecall
                }
            )
            .then(response => {
                return response;
            }).catch(function (error) {
                if (error.response) {
                    return error.response.data.errors.children;
                }
            });
    }

    registerToEvent(event) {

    }

    mailUpdate(mail, user) {
        return this
            .patch("/api/candidate/mail/update/"+user,
                {
                    "mail":mail
                }
            )
            .then(response => {
                return response;
            }).catch(function (error) {
                if (error.response) {
                    return error.response.data.errors.children;
                }
            });
    }

    cvUpdate(cv, user) {
        return this
            .patch("/api/candidate/cv/update/"+user,
                {
                    "cv":cv
                }
            )
            .then(response => {
                return response;
            }).catch(function (error) {
                if (error.response) {
                    return error.response.data.errors.children;
                }
            });
    }

    infosUpdate(phone,mail,diplome,mobility,city,sector,user) {
        return this
            .patch("/api/candidate/infos/update/"+user,
                {
                    "phone":phone,
                    "mail":mail,
                    "diplome":diplome,
                    "mobility":mobility,
                    "city":city,
                    "sector":sector
                }
            )
            .then(response => {
                return response;
            }).catch(function (error) {
                if (error.response) {
                    return error.response.data.errors.children;
                }
            });
    }

    preregister(event, mail) {
        return this
            .post("/api/event/"+event+"/preregistration",
                {
                    "email":mail
                }
            )
            .then(response => {
                return response;
            });
    }

    contact(event, from, name, firstName, email, phone, message) {
        return this
            .post("/api/event/"+event+"/contact",
                {
                    "from":from,
                    "name":name,
                    "firstName":firstName,
                    "email":email,
                    "phone":phone,
                    "message":message,
                }
            )
            .then(response => {
                return response;
            });
    }

    logout() {
		return axios.get('/auth/logout').then(() => {location.reload();});
    }
    
    sectionCheck(id){
        return axios
            .get("/api/sections/"+id)
            .then(response => {return response.data;})
            .catch(function (error) {
                if (error.response) {
                    return error.response.data.errors.children;
                }
            });
    }
    
    async check() {
		if(null === cookies.getItem('_jf_ts')) {
			return axios.get("/api/user/me")
				.then(() => {return true})
				.catch(e => {console.log(e);});
		}
		return new Promise((resolve, reject) => {resolve(true);});
	}
    
    async get(uri) {
		return await this.check()
		.then((r) => {return axios.get(uri);})
		.catch(e => console.log(e));
	}
	
	async post(uri, json) {
		return await this.check()
		.then(() => {return axios.post(uri, json);})
		.catch(e => {console.log(e);throw e;});
	}

    async patch(uri, json) {
        return await this.check()
            .then(() => {return axios.patch(uri, json);})
            .catch(e => {console.log(e);throw e;});
    }
}
export default new AuthService();
