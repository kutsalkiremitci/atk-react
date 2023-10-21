import i18n from "i18next";
import { initReactI18next } from "react-i18next";


import tr from '~/locales/tr.json'
import en from '~/locales/en.json'
const resources = {
    tr: {
        translation: tr
    },
    en:{
        translation: en
    }
}

i18n
.use(initReactI18next)
.init({
    lng: localStorage.getItem('lng') ?? 'tr',
    fallbackLng: 'tr', // default
    resources
})

export default i18n;