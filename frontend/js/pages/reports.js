import {api} from '../api/client.js';
export const ticketReport=query=>api(`/reports/tickets${query||'?group_by=status'}`);
