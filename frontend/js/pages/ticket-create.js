import {api} from '../api/client.js';
export const createTicket=data=>api('/tickets',{method:'POST',body:JSON.stringify(data)});
