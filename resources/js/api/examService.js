import apiClient from './client';

export const examService = {
  async getHistory() {
    const { data } = await apiClient.get('/exams');
    return data;
  },

  async getExam(uuid) {
    const { data } = await apiClient.get(`/exams/${uuid}`); 
    return data;
  },

  async startExam(form) {
    const { data } = await apiClient.post('/exams/start', form);
    return data;
  },

  async submitAnswer(uuid, order, isCorrect, userAnswer = null) {
    const { data } = await apiClient.post(`/exams/${uuid}/answer/${order}`, {
      is_correct: isCorrect,
      user_answer: userAnswer
    });
    return data;
  },

  async markAnswerCorrect(uuid, answerId) {
    const { data } = await apiClient.post(`/exams/${uuid}/mark_correct/${answerId}`);
    return data;
  }
};