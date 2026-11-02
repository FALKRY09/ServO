import {api} from '../api/client.js';
export const profile=()=>api('/auth/me');
