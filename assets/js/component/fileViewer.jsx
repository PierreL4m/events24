import React, { useEffect, useRef } from 'react';
import PSPDFKit from "pspdfkit";

export default function PdfViewerComponent(props) {
    const containerRef = useRef(null);

    useEffect(() => {
        const container = containerRef.current;
        let instance;
        (async function () {
            PSPDFKit.unload(container);

            instance = await PSPDFKit.load({
                // Container where PSPDFKit should be mounted.
                container,
                // The document to open.
                document: props.document,
                // Use the public directory URL as a base URL. PSPDFKit will download its library assets from here.
                baseUrl: `http://24event.local.com/public`,
            });
        })();

        return () => PSPDFKit && PSPDFKit.unload(container);
    }, []);

    return (
        <div
            ref={containerRef}
            style={{ width: '100%', height: '100vh' }}
        />
    );
}