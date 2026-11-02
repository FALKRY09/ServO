export function token(){return sessionStorage.getItem('servo_token')}
export function clearSession(){sessionStorage.removeItem('servo_token')}
export function role(user){return user?.role?.name || null}
