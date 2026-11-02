import {api} from '../api/client.js';
export const ticketMessages=id=>api(`/tickets/${id}/messages`);
