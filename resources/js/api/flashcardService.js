import apiClient from './client';

export const flashcardService = {
  async getFlashcards(set_id) {
    const { data } = await apiClient.get(`/${set_id}/flashcards`); 
    return data;
  },

  async addFlashcards(set_id, question, answer, notes, set, image) {
    const { data } = await apiClient.post(`/${set_id}/flashcards`, {
      question: question.trim(),
      answer: answer.trim(),
      notes: notes.trim(),
      set: set,
      image: image
    }, {
      headers: {'Content-Type': 'multipart/form-data'}})
    return data;
  },

  async updateFlashcard(setId, flashcardId,question,answer,notes) {
    const { data } = await apiClient.put(`/${setId}/flashcards/${flashcardId}`, {
      question: question.trim(),
      answer: answer.trim(),
      notes: notes.trim(),
    })
    return data;
  },


  async markLearn(id, learned= true) {
    const { data } = await apiClient.put(`/flashcards/${id}/learned`, {
      learned: learned,
    }); 
    return data;
  },

  async deleteFlashcard(flashcardId) {
    const { data } = await apiClient.delete(`/flashcards/${flashcardId}`)
    return data;
  },

  async import(id,file) {
    const form = new FormData()
    form.append('file', file)
    const { data } = await apiClient.post(`/${id}/flashcards/import`, form)
    return data;
  },


  async removeImage(cardId) {
    const { data } = await apiClient.delete(`/flashcards/${cardId}/image`)
    return data
  },

  async updateImage(cardId, image) {
    const { data } = await apiClient.put(`/flashcards/${cardId}/image`, {
      image: image
    }, {
      headers: {'Content-Type': 'multipart/form-data'}})
    return data
  },

  async updateWrongAnswer(optionId,optionText) {
    const { data } = await apiClient.put(`/option/${optionId}`, {
      text: optionText.trim()
    })
    return data
  },

  async addWrongAnswer(cardId,optionText) {
    const { data } = await apiClient.post(`/flashcards/${cardId}/option`, {
      text: optionText
    })
    return data
  }
};