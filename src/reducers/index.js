import { combineReducers } from 'redux';
import domains from '~/reducers/domains';
import auth from '~/reducers/auth';

const combinedReducers = combineReducers({ domains,auth });

export default combinedReducers