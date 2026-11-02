import {resourcePage} from './_resource.js';
export const renderUsers=()=>resourcePage('/users',['name','email','is_active']);
