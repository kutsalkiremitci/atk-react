import { createSlice } from '@reduxjs/toolkit'
const initialState = [];

export const domainSlice = createSlice({
    name: 'domains',
    initialState: initialState,
    reducers: {
        list: (state, action) => {
            return action.payload;
        },
        add: (state, action) => {
           state.push(action.payload)
        },
        update: (state, action) => {
            const payload = action.payload;
            state = state.map(domain => {
                if(domain.id === action.payload.id){
                    return domain = {...domain, ...payload};
                }
                return domain;
            })
            
            return state;
        },
        remove: (state, action) => {
            return state.filter(domain => domain.id !== action.payload.id)
        },
    },
})

// Action creators are generated for each case reducer function
export const { list, add, update, remove } = domainSlice.actions

export default domainSlice.reducer