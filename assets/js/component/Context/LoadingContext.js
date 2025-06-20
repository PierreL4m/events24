// LoadingContext.js
import React,{ createContext, useContext, useState } from "react";

const LoadingContext = createContext({
    loading: true,
    setLoading: null,
});

export function LoadingProvider({ children }) {
    const [loading, setLoading] = useState(true);
    const value = { loading, setLoading };
    return (
        <LoadingContext.Provider value={value}>{children}</LoadingContext.Provider>
    );
}

export function useLoading() {
    const context = useContext(LoadingContext);
    if (!context) {
        throw new Error("useLoading must be used within LoadingProvider");
    }
    return context;
}