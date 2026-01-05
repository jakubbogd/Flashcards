import axios from 'axios';

const userClient = axios.create({
  baseURL: '/user',
  headers: {
    'Content-Type': 'application/json',
  },
});

userClient.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('Błąd User:', error.response?.data || error.message);
    return Promise.reject(error);
  }
);

export default userClient;