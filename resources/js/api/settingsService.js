import apiClient from './client';

export const settingsService = {

  async getSettings() {
    const { data } = await apiClient.get(`/settings}`); 
    return data;
  },

  async updateSettings(dark_mode) {
    const { data } = await apiClient.put(`/settings`, {
      dark_mode: dark_mode
    }); 
    return data;
  },


};