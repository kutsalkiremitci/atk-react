import { Route, Routes } from 'react-router-dom';
import { authData } from '~/auth/authWrapper'

/* Pages */
import Login from '~/pages/login';
import Dashboard from '~/pages/dashboard';
import DomainPrices from '~/pages/domain-prices';
import HostingPrices from '~/pages/hosting-prices';
import ServiceTypes from '~/pages/service-types';
import Customers from '~/pages/customers';


// burası farklı yere alınacak... örnek routes.js
const routes = [
    {
        name: "default",
        path: "/",
        element: <Login />,
        isPrivate: false
    },
    {
        name: "login",
        path: "login",
        element: <Login />,
        isPrivate: false
    },
    {
        name: "Dashboard",
        path: "dashboard",
        element: <Dashboard />,
        isPrivate: true
    },
    {
        name: "Customers",
        path: "customers",
        element: <Customers />,
        isPrivate: true
    },
    {
        name: "Domain Prices",
        path: "domain-prices",
        element: <DomainPrices />,
        isPrivate: true
    },
    {
        name: "Hosting Prices",
        path: "hosting-prices",
        element: <HostingPrices />,
        isPrivate: true
    },
    {
        name: "Service Types",
        path: "services-types",
        element: <ServiceTypes />,
        isPrivate: true
    },
]
export default function AuthRoutes() {

    const { user } = authData();
    const isAuthenticated = user.isAuthenticated;
    return (
        <Routes>
            {routes.map((r, key) => {
                if (isAuthenticated && r.isPrivate) {
                    return <Route key={key} path={`/${r.path}`} element={r.element} />
                } else if (!r.isPrivate) {
                    return <Route key={key} path={`/${r.path}`} element={r.element} />
                } else return null;
            })}
        <Route path="/*" element={isAuthenticated ? <Dashboard /> : <Login />} />
        </Routes>
    )
}