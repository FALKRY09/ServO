import {api} from '../api/client.js';
export const assignTicket=(id,assignee_id)=>api(`/tickets/${id}/assignments`,{method:'POST',body:JSON.stringify({assignee_id})});
