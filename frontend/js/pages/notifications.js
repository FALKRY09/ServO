import {api} from '../api/client.js';
export const notifications=()=>Promise.all([api('/notifications'),api('/notifications/unread-count')]);
