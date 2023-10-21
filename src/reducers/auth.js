import { createSlice } from '@reduxjs/toolkit'
const initialState = [];

export const authSlice = createSlice({
    name: 'auth',
    initialState: initialState,
    reducers: {
        get: (state, action) => {
            console.log("calisti")
            state.push(...action.payload)
        },
        add: (state, action) => {
           state.push(action.payload)
        },
        update: (state, action) => {
            return state.map(domain => {
                if (domain.id === action.payload.id) {
                    domain = action.payload
                }
                return domain;
            })
        },
        remove: (state, action) => {
            return state.filter(domain => domain.id !== action.payload.id)
        },
    },
})

// Action creators are generated for each case reducer function
export const { get, add, update, remove } = authSlice.actions

export default authSlice.reducer