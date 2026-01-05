import userClient from './userClient';

export const userService = {

  async getUser() {
    const { data } = await userClient.get('/', { withCredentials: true });
    return data;
  },

  async updateProfile(form) {
    const { data } = await userClient.put('/profile-information', form);
    return data;
  },

  async updatePassword(form) {
    const { data } = await userClient.put('/password', form);
    return data;
  },

};