import {api} from '../api/client.js';
export const renderTicketDetail=id=>api(`/tickets/${id}`);
