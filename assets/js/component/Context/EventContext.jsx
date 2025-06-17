import React, {createContext, useState} from "react";

const initialState = {

    id : 0,
    slug : ""

};

export const Context = createContext();

const EventContext = ({children}) => {
  const [state, setState] = useState(initialState);
  return(
     <Context.Provider value={[state, setState]}>{children}</Context.Provider>
  );
};

export default EventContext;
