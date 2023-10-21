import React from 'react'
import ReactDOM from 'react-dom/client'
// import { BrowserRouter } from 'react-router-dom'

import App from './App.jsx'
// import i18n from './i18n.js'

import 'react-toastify/dist/ReactToastify.css';
import "primereact/resources/themes/tailwind-light/theme.css"
import './public/css/index.css'

import { store } from '~/store'
import { Provider } from 'react-redux'


ReactDOM.createRoot(document.getElementById('root')).render(
    <Provider store={store}>
        <App />
    </Provider>
)
