import {resourcePage} from './_resource.js';
export const renderTickets=()=>resourcePage('/tickets',['ticket_number','title','status','created_at']);
