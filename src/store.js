import { configureStore } from '@reduxjs/toolkit'
import combinedReducers from './reducers'

export const store = configureStore({
  reducer: combinedReducers,
})

