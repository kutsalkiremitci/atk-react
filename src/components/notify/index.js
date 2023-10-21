import { toast } from 'react-toastify'

export default function $notify (props){

    const message = props.message ?? "Mesaj içeriği tanımlanmamış!";
    const status = props.status ?? 200;
    let type = props.type ?? null;
    /* 
        Types = info,success,warning,error,default
    */

    /* 
       toast.promise(
           response,{
             pending: 'Promise is pending',
             success: {
                render({ data }){
                    //... işlemler
                    return 'Promise resolved 👌';
                }
             },
             error: 'Promise rejected 🤯'
           },
       )
    */

    if(type == null){
        if(status >= 200 && status <= 226){
            type = "success"
        }else if(status >= 400 && status <= 451){
            type = "error"
        }else if(status >= 500){
            type = "warning";
        }else{
            type = "info";
        }
    }
    

    return toast[type](message,{ autoClose: type == 'success' ? 500 : 2000});

}