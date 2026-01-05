import apiClient from './client';

export const hierarchyService = {
  async getSets() {
    const { data } = await apiClient.get(`/sets`); 
    return data;
  },

  async getFolders() {
    const { data } = await apiClient.get(`/folders`); 
    return data;
  },

  async addFolder(name) {
    const { data } = await apiClient.post(`/folders`, {
      name: name.trim()
    }); 
    console.log(data)
    return data;
  },

  async addSet(name,description,folder_id) {
    const { data } = await apiClient.post(`/sets`, {
      name: name.trim(),
      description: description.trim(),
      folder_id: folder_id
    }); 
    return data;
  },

  async updateSet(id, name,description) {
    const { data } = await apiClient.put(`/sets/${id}`, {
      name: name.trim(),
      description: description.trim(),
    }); 
    return data;
  },

  async deleteSet(id) {
    const { data } = await apiClient.delete(`/sets/${id}`)
    return data;
  },

};