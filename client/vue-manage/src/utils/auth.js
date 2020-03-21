import Cookies from 'js-cookie'

const TokenKey = 'manage-token'

export function getToken () {
  // localStorage.getItem(TokenKey);
  return Cookies.get(TokenKey)
}

export function setToken (token) {
  // localStorage.setItem(TokenKey,token);
  return Cookies.set(TokenKey, token)
}

export function removeToken () {
  // localStorage.remove(TokenKey);
  return Cookies.remove(TokenKey)
}
