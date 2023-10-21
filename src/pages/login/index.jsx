import { memo, useEffect, useState } from "react";
import { useTranslation } from "react-i18next";
import { config, $api, initApi } from '~/config.js';
import { useNavigate } from "react-router-dom";
import { authData } from "~/auth/authWrapper";



export default function Login() {
    const navigate = useNavigate();
    const { user, login } = authData();
    const { t, i18n } = useTranslation();

    const [email, setEmail] = useState('demo@demo.com');
    const [password, setPassword] = useState('demo');

    const doLogin = async (e) => {
        e.preventDefault()

        if (email.length == 0 || password.length == 0) {
            return alert("E-posta veya parola boş bırakılamaz!");
        }

        const isLogin = await login({
            email,
            password
        })

        if (isLogin) {
            initApi()
            navigate('/dashboard');
        }
        return;
    }


    useEffect(() => {
       if(user.isAuthenticated){
            navigate('/dashboard');
       }
    },[])


    // const checkAuth = () => {
    //     setLoading(true)
    //     setTimeout(() => {
    //         setLoading(false)
    //         console.log(1)
    //         navigate('/dashboard');
    //     },2000)
    // }

    // useEffect(() => {
    //     checkAuth()
    // },[])

    // if(user.isAuthenticated){
    //     return navigate('/dashboard')
    // }

    return (
        <>
            <section className="bg-gray-50 dark:bg-gray-900">
                <div className="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                    <a href="#" className="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                        ATKHosting
                    </a>
                    <div className="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                        <div className="p-6 space-y-4 md:space-y-6 sm:p-8">
                            <form className="space-y-4 md:space-y-6" onSubmit={e => doLogin(e)}>
                                <div>
                                    <label
                                        className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{t('E-Posta')}</label>
                                    <input
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        type="email"
                                        name="email"
                                        id="email"
                                        placeholder={t('example@gmail.com')}
                                        className="focus:outline-0  bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                                </div>
                                <div>
                                    <label className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{t('Parola')}</label>
                                    <input
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        type="password"
                                        name="password"
                                        placeholder="••••••••"
                                        className="bg-gray-50 focus:outline-0 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                                </div>
                                <button type="submit" className="w-full bg-slate-500 hover:bg-slate-600 text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">{t('Giriş Yap')}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </>
    )
}


