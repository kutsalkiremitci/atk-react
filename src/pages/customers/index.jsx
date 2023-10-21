import { authData } from "~/auth/authWrapper"

export default function Customers(){

    const { user } = authData();

    return (
        <>
            Customers
        </>
    )
}