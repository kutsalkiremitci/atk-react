import axios from 'axios'

let config = {
  API_URL: "http://localhost/atk-react/server/api",
}
axios.defaults.baseURL = config.API_URL;

const initApi = () => {

  let user = JSON.parse(localStorage.getItem('user'));

  if (user && user.token && user.token != '') {
    axios.defaults.headers.common['Authorization'] = `Bearer ${user.token}`;
  }

  // first response point
  axios.interceptors.response.use(function (response) {
    // token valid değilse.
    if (typeof response.data.tokenIsValid != 'undefined' && !response.data.tokenIsValid) {
      localStorage.removeItem('user')
      location.href = location.href;
    }

    return response;
  }, function (error) {
    // Do something with response error
    return Promise.reject(error);
  });
}


initApi()


export {
  initApi,
  axios as $api,
  config
}