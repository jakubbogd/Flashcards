import { createApp } from 'vue'

import FlashcardsCreate from './components/FlashcardsCreate.vue'
import FlashcardsLearn from './components/FlashcardsLearn.vue'
import FlashcardsHome from './components/FlashcardsHome.vue'
import FlashcardsSet from './components/FlashcardsSet.vue'
import ExamStart from './components/ExamStart.vue'
import ExamSession from './components/ExamSession.vue'
import ExamResult from './components/ExamResult.vue'
import ExamHistory from './components/ExamHistory.vue'
import SmartLearnStart from './components/SmartLearnStart.vue'
import SmartLearnSession from './components/SmartLearnSession.vue'
import Settings from './components/Settings.vue'
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import '../css/global.css'

const el = document.getElementById('app')
if (el) {
    const page = el.dataset.page

    if (page === 'login') {
        createApp(Login).mount('#app')
    }

    if (page === 'register') {
        createApp(Register).mount('#app')
    }

    if (page === 'create') {
        createApp(FlashcardsCreate).mount('#app')
    }

    if (page === 'learn') {
        createApp(FlashcardsLearn).mount('#app')
    }

    if (page === 'home') {
        createApp(FlashcardsHome).mount('#app')
    }

    if (page === 'sets') {
        createApp(FlashcardsSet).mount('#app')
    }

    if (page === 'exam') {
        createApp(ExamStart).mount('#app')
    }
    
    if (page === 'exam-s') {
        createApp(ExamSession).mount('#app')
    }
    
    if (page === 'exam-r') {
        createApp(ExamResult).mount('#app')
    }

    if (page === 'exam-h') {
        createApp(ExamHistory).mount('#app')
    }

    if (page === 'smart-learn-start') {
        createApp(SmartLearnStart).mount('#app')
    }

    if (page === 'smart-learn-session') {
        createApp(SmartLearnSession).mount('#app')
    }

    if (page === 'settings') {
        createApp(Settings).mount('#app');
    }
}

if (localStorage.getItem('dark_mode') === '1') {
    document.body.classList.add('dark')
  }