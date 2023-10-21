import { Menubar } from "primereact/menubar";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { $api } from "~/config";

export default function Header() {
    let menus = [
        {
            name: "Panel",
            url: "dashboard"
        },
        {
            name: "Domain Fiyatları",
            url: "domain-prices"
        },
        {
            name: "Hosting Fiyatları",
            url: "hosting-prices"
        },
        {
            name: "Müşteriler",
            url: "customers"
        },
        {
            name: "Servis Tipleri",
            url: "service-types"
        },
    ]

    const path = useLocation().pathname;

    const logout = () => {
        let user = JSON.parse(localStorage.getItem('user'));
        $api.post('/auth/logout', {
            token: user.token
        }).then(r => {
            localStorage.removeItem('user')
            location.href = "/login";
        }).catch(err => {
            alert(err)
        })
    }


    return (
        <header className="bg-neutral-800 h-[75px] flex items-center px-3 gap-4">
            {menus.map((menu, key) => (
                <div key={key}>
                    <Link to={`/${menu.url}`} className={`${path.slice(1) == menu.url ? 'text-gray-300 font-bold' : ''} text-white p-3 transition-all text-[16px] hover:border hover:text-gray-300`}>{menu.name}</Link>
                </div>
            ))}

            <div href="#" className="text-white hover:opacity-80 transition-all text-[15px] cursor-pointer" onClick={() => logout()}>Çıkış Yap</div>
        </header>
    )
}