import apiClient from './client';

export const smartLearnService = {

  async getSession(uuid) {
    const { data } = await apiClient.get(`/smart-learn/${uuid}`); 
    return data;
  },

  async postAnswer(uuid, order, option_txt, option_id, mode) {
    const { data } = await apiClient.post(`/smart-learn/${uuid}/answer/${order}`, {
      option_txt: option_txt,
      option_id: option_id,
      mode: mode
    }); 
    return data;
  },

  async startSession(set_ids, count) {
    const { data } = await apiClient.post(`/smart-learn/start`, {
      set_ids: set_ids,
      count: count
    }); 
    return data;
  },

};