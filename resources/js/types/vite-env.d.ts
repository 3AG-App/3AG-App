/// <reference types="vite/client" />

interface ImportMetaEnv {
    readonly VITE_GLEAP_SDK_TOKEN?: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}
