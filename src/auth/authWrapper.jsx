import { createContext, memo, useContext, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useNavigate } from 'react-router-dom';
import Header from '~/components/header';
import { $api } from '~/config';
import AuthRoutes from '~/routes/AuthRoutes';

const AuthContext = createContext();

export const authData = () => useContext(AuthContext);

export const AuthWrapper = memo(() => {
    const navigate = useNavigate();
    const { t } = useTranslation()

    const checkUserToken = async () => {

        const user = JSON.parse(localStorage.getItem('user'));

        if (user == null) {
            return new Promise((resolve,reject) => {
                reject(false)
            })
        };

        const token = user.token;

        let response = await $api.post('/auth/check-user-token', { token })

        return response;
    };

    // let userStateDefaults = checkUserStorage();

    const [IsAuthorization,setAuthorization] = useState(true);
    const [user, setUser] = useState({
        data: {}, // kullanıcı veri
        isAuthenticated: false // kullanıcı login durumu
    });


    const login = async (payload) => {
        try {
            const r = await $api.post('/auth/login', payload);
            if (r.status !== 200) {
                return false;
            }

            // alert(t('Başarılı'));
            localStorage.setItem('user', JSON.stringify(r.data.user));
            setUser({
                data: r.data.user,
                isAuthenticated: true
            });
            return true;

        } catch (err) {
            console.log(err)
            alert(err.response.data.message);
            return false;
        }
    }
    const logout = async () => {
        try {
            const r = await $api.post('/auth/logout', { token: user.data.token })

            if(r.status != 200){
                return false;   
            }

            localStorage.removeItem('user')
            setUser({
                data: {},
                isAuthenticated: false
            })

            return true;
        } catch (err) {
            console.log(err)
            return false;
        }
    }

    useEffect(() => {
        checkUserToken().then(response => {
            setUser({
                data: JSON.parse(localStorage.getItem('user')),
                isAuthenticated: true
            })
            setAuthorization(false)
        }).catch(err => {
            localStorage.removeItem('user')
            setAuthorization(false)
            navigate('/login');
        })
    },[])


    const globals = { user, setUser, login, logout };
    return (
        <>
            <AuthContext.Provider value={globals}>
                {user.isAuthenticated && (
                    <Header />
                )}
                {IsAuthorization ? (
                    <>
                        Auth Kontrol ediliyor
                    </>
                )
                :
                (<AuthRoutes />)}
            </AuthContext.Provider>
        </>
    )
});




