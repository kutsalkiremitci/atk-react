// import { useTranslation } from 'react-i18next';
import { AuthWrapper } from '~/auth/authWrapper'
import { BrowserRouter } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';

export default function App() {

  // const { t, i18n } = useTranslation();
  // const changeLanguage = lng => {
  //   i18n.changeLanguage(lng)
  //   localStorage.setItem('lng', lng)
  // }


  return (
    <>
      <BrowserRouter>
        <ToastContainer theme="colored" />
        {/* Aktif Dil: {i18n.language} */}
  
        {/* <button onClick={() => changeLanguage('tr')}>Türkçe</button>
        <button onClick={() => changeLanguage('en')}>İngilizce</button> */}
        <main>
          <AuthWrapper />
        </main>
      </BrowserRouter>

    </>
  )
}
