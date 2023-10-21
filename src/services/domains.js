import { config, $api } from "~/config";
import { LIST_DOMAINS, ADD_DOMAIN,REMOVE_DOMAIN, UPDATE_DOMAIN } from "~/types/domains";
import { store } from '~/store'
// import $notify from "~/components/notify";
// import { toast } from "react-toastify";

const API_SUFFIX = "/domain";


export default function(){
    const list = async () => {
        try {
            const { data } = await $api.get(API_SUFFIX + "/list")
    
            store.dispatch({
                type: LIST_DOMAINS,
                payload: data.data
            })
    
            return data;
        } catch ({ response }) {
            return response?.data
        }
    
    }
    const add = async (payload) => {
        try {
            const { data } = await $api.post(API_SUFFIX + "/add", payload);

            store.dispatch({
                type: ADD_DOMAIN,
                payload: {
                    id: data.lastInsertId,
                    ...payload
                }
                
            })
    
            return data;
        } catch ({ response }) {
            return response?.data;
        }
    }  
    const update = async (payload) => {
        try {
            const { data } = await $api.post(API_SUFFIX + "/update", payload);
    
            store.dispatch({
                type: UPDATE_DOMAIN,
                payload
            })
    
            return data;
        } catch ({ response }) {
            return response?.data;
        }
    }  
    const remove = async (payload) => {
        try {
            const { data } = await $api.post(API_SUFFIX + "/remove", payload);
    
            store.dispatch({
                type: REMOVE_DOMAIN,
                payload
            })
    
            return data;
        } catch ({ response }) {
            return response?.data;
        }
    }   

    return { list,add,update,remove }
}