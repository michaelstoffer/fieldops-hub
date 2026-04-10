// resources/js/bootstrap.js

import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// If you want lodash too:
// import _ from 'lodash';
// window._ = _;
