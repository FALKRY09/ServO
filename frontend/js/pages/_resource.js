import {api} from '../api/client.js';
import {table} from '../components/table.js';
export async function resourcePage(endpoint, columns){const result=await api(endpoint);return {html:table(result.data,columns),meta:result};}
