<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, nextTick, watch, onUnmounted, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  reps: {
    type: Array,
    default: () => [],
  },
  currentPartnerId: {
    type: Number,
    default: null,
  },
})

// always safe (never undefined)
const safeReps = computed(() => props.reps || [])

const partnerId = ref(props.currentPartnerId || null)
const partnerName = ref('')
const loading = ref(false)
const sending = ref(false)
const messages = ref([])
const newMessage = ref('')
const file = ref(null)
const poller = ref(null)
const listEl = ref(null)

// Static demo messages (used as fallback / preview)
const demoMessages = [
  {
    id: 1,
    mine: false,
    sender: 'Admin',
    body: 'Good morning, did you start the Kurunegala route?',
    time: '09:02 AM',
  },
  {
    id: 2,
    mine: true,
    sender: 'You',
    body: 'Yes, I just completed Ranjan Stores. Moving to Sahan Stores next.',
    time: '09:05 AM',
  },
  {
    id: 3,
    mine: false,
    sender: 'Admin',
    body: 'Great. Please remember to update cheque collections separately.',
    time: '09:06 AM',
  },
]

function scrollToBottom() {
  nextTick(() => {
    if (listEl.value) listEl.value.scrollTop = listEl.value.scrollHeight
  })
}

async function loadMessages() {
  if (!partnerId.value) return
  loading.value = true
  try {
    const { data } = await axios.get(route('chat.messages'), {
      params: { user_id: partnerId.value },
      headers: { Accept: 'application/json' },
    })
    const loaded = data.messages || []
    messages.value = loaded.length ? loaded : demoMessages
    scrollToBottom()
    await axios.post(route('chat.read'), { user_id: partnerId.value })
  } catch (e) {
    console.error('Chat load error:', e)
    messages.value = demoMessages
  } finally {
    loading.value = false
  }
}

async function send() {
  if (sending.value) return
  if (!newMessage.value && !file.value?.files?.[0]) return
  sending.value = true
  try {
    const fd = new FormData()
    fd.append('receiver_id', partnerId.value)
    if (newMessage.value) fd.append('body', newMessage.value)
    if (file.value?.files?.[0]) fd.append('attachment', file.value.files[0])

    const { data } = await axios.post(route('chat.send'), fd, {
      headers: { 'Content-Type': 'multipart/form-data', Accept: 'application/json' },
    })
    messages.value.push(data.item)
    newMessage.value = ''
    if (file.value) file.value.value = null
    scrollToBottom()
  } finally {
    sending.value = false
  }
}

function selectPartner(id, name) {
  if (partnerId.value === id) return
  partnerId.value = id
  partnerName.value = name
  messages.value = []
  loadMessages()
}

onMounted(() => {
  const first = safeReps.value?.[0]
  if (partnerId.value && !partnerName.value) {
    const found = safeReps.value.find(r => r.id === partnerId.value)
    partnerName.value = found?.name || ''
  } else if (first && !partnerId.value) {
    partnerId.value = first.id
    partnerName.value = first.name
  }
  if (partnerId.value) {
    loadMessages()
  }
  poller.value = setInterval(loadMessages, 3000)
})

watch(partnerId, () => {
  const found = safeReps.value.find(r => r.id === partnerId.value)
  partnerName.value = found?.name || ''
})

onUnmounted(() => {
  if (poller.value) clearInterval(poller.value)
})

// Search for reps list
const repSearch = ref('')
const filteredReps = computed(() => {
  const list = safeReps.value
  if (!repSearch.value) return list
  return list.filter(r =>
    r.name.toLowerCase().includes(repSearch.value.toLowerCase())
  )
})
</script>

<template>
  <AdminLayout>
    <Head title="Chat" />

    <div class="content-header chat-header">
      <div class="container-fluid">
        <div class="row mb-2 align-items-center">
          <div class="col-sm-6">
            <h4 class="m-0 font-weight-bold">Chat Center</h4>
            <p class="mb-0 text-muted small">
              Real-time communication between head office and sales representatives.
            </p>
          </div>
          <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="header-pill">
              <i class="fas fa-circle text-success mr-1"></i>
              Live connection (auto-refresh every 3 seconds)
            </span>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- Left: reps list -->
          <div class="col-md-3 mb-3 mb-md-0">
            <div class="card reps-card">
              <div class="card-header reps-header">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>Representatives</strong>
                  <span class="badge badge-light small">{{ safeReps.length }}</span>
                </div>
                <div class="input-group input-group-sm mt-2">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-right-0">
                      <i class="fas fa-search text-muted"></i>
                    </span>
                  </div>
                  <input
                    v-model="repSearch"
                    type="text"
                    class="form-control form-control-sm border-left-0"
                    placeholder="Search rep…"
                  />
                </div>
              </div>

              <div class="list-group list-group-flush reps-list">
                <button
                  v-for="rep in filteredReps"
                  :key="rep.id"
                  class="list-group-item list-group-item-action rep-item"
                  :class="{'active': rep.id === partnerId}"
                  @click="selectPartner(rep.id, rep.name)"
                >
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle mr-2">
                      <span>{{ rep.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div class="flex-grow-1 text-left">
                      <div class="rep-name">{{ rep.name }}</div>
                      <div class="rep-status text-muted small">
                        {{ rep.id === partnerId ? 'Active conversation' : 'Tap to open chat' }}
                      </div>
                    </div>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Right: conversation -->
          <div class="col-md-9">
            <div class="card chat-card">
              <!-- Top chat header -->
              <div class="card-header chat-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <div class="avatar-large mr-3" v-if="partnerName">
                    <span>{{ partnerName.charAt(0).toUpperCase() }}</span>
                    <span class="status-dot"></span>
                  </div>
                  <div>
                    <div class="chat-title">
                      {{ partnerName || 'Select a representative' }}
                    </div>
                    <div class="chat-subtitle text-muted small" v-if="partnerName">
                      <i class="fas fa-circle text-success mr-1"></i> Online • Mobile App
                    </div>
                    <div class="chat-subtitle text-muted small" v-else>
                      Choose a representative from the left to start chatting.
                    </div>
                  </div>
                </div>
                <div>
                  <span v-if="loading" class="badge badge-info mr-2">Syncing…</span>
                  <button
                    type="button"
                    class="btn btn-xs btn-outline-secondary"
                    @click="loadMessages"
                    :disabled="!partnerId || loading"
                  >
                    <i class="fas fa-sync mr-1"></i> Refresh
                  </button>
                </div>
              </div>

              <!-- Messages list -->
              <div
                class="card-body chat-body"
                ref="listEl"
              >
                <div v-if="!partnerId" class="text-center text-muted mt-5">
                  <i class="far fa-comments fa-2x mb-3 d-block"></i>
                  Select a representative to view the conversation.
                </div>

                <template v-else>
                  <div
                    v-for="m in messages"
                    :key="m.id"
                    class="d-flex mb-3 message-row"
                    :class="m.mine ? 'justify-content-end' : 'justify-content-start'"
                  >
                    <!-- Incoming -->
                    <div
                      v-if="!m.mine"
                      class="d-flex align-items-end"
                    >
                      <div class="avatar-xs mr-2">
                        <i class="fas fa-user"></i>
                      </div>
                      <div class="message-bubble message-in">
                        <div class="message-meta small text-muted mb-1">
                          {{ m.sender || 'Rep' }}
                        </div>
                        <div class="message-text">
                          {{ m.body }}
                        </div>
                        <div
                          v-if="m.attachment_url"
                          class="mt-1"
                        >
                          <a
                            :href="m.attachment_url"
                            target="_blank"
                            class="attachment-link"
                          >
                            <i class="fas fa-paperclip mr-1"></i> Attachment
                          </a>
                        </div>
                        <div class="message-time small text-muted mt-1 text-right">
                          {{ m.time }}
                        </div>
                        <span class="bubble-tail-left"></span>
                      </div>
                    </div>

                    <!-- Outgoing -->
                    <div
                      v-else
                      class="d-flex align-items-end"
                    >
                      <div class="message-bubble message-out">
                        <div class="message-text">
                          {{ m.body }}
                        </div>
                        <div
                          v-if="m.attachment_url"
                          class="mt-1"
                        >
                          <a
                            :href="m.attachment_url"
                            target="_blank"
                            class="attachment-link text-white"
                          >
                            <i class="fas fa-paperclip mr-1"></i> Attachment
                          </a>
                        </div>
                        <div class="message-time small text-right mt-1">
                          {{ m.time }}
                          <i class="fas fa-check-double ml-1 read-tick"></i>
                        </div>
                        <span class="bubble-tail-right"></span>
                      </div>
                    </div>
                  </div>
                </template>
              </div>

              <!-- Composer -->
              <div class="card-footer chat-footer">
                <form @submit.prevent="send" class="d-flex align-items-center">
                  <button
                    type="button"
                    class="btn btn-icon mr-2"
                    title="Emoji (UI only)"
                  >
                    <i class="far fa-smile"></i>
                  </button>

                  <!--
                  <label class="btn btn-icon mr-2 mb-0">
                    <i class="fas fa-paperclip"></i>
                    <input ref="file" type="file" class="d-none" />
                  </label>
                  -->

                  <input
                    v-model="newMessage"
                    type="text"
                    class="form-control chat-input mr-2"
                    :placeholder="partnerId ? 'Type your message…' : 'Select a representative first'"
                    :disabled="!partnerId"
                  />

                  <button
                    class="btn btn-primary chat-send-btn"
                    :disabled="sending || !partnerId || (!newMessage && !file?.files?.[0])"
                  >
                    <span v-if="sending">
                      <i class="fas fa-circle-notch fa-spin mr-1"></i> Sending…
                    </span>
                    <span v-else>
                      <i class="fas fa-paper-plane mr-1"></i> Send
                    </span>
                  </button>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  </AdminLayout>
</template>

<style scoped>
.chat-header {
  background: linear-gradient(120deg, #0f172a, #1f2937);
  color: #fff;
  border-radius: 0 0 1.4rem 1.4rem;
  padding-bottom: 1rem;
  margin-bottom: 0.75rem;
}

.chat-header h4 {
  font-size: 1.4rem;
}

.header-pill {
  border-radius: 999px;
  background: rgba(15, 23, 42, 0.9);
  padding: 0.35rem 0.9rem;
  font-size: 0.78rem;
  color: #e5e7eb;
}

/* Left reps list */
.reps-card {
  border-radius: 1rem;
  box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
  overflow: hidden;
}

.reps-header {
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}

.reps-list {
  max-height: 70vh;
  overflow-y: auto;
}

.rep-item {
  border: 0;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.85rem;
  padding-top: 0.6rem;
  padding-bottom: 0.6rem;
}

.rep-item:last-child {
  border-bottom: none;
}

.rep-item.active {
  background: linear-gradient(120deg, #6366f1, #3b82f6);
  color: #f9fafb;
}

.rep-item.active .rep-status {
  color: #e5e7eb !important;
}

.avatar-circle {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  background: #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.9rem;
  color: #4b5563;
}

.rep-name {
  font-weight: 600;
}

/* Right chat panel */
.chat-card {
  border-radius: 1rem;
  box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
  display: flex;
  flex-direction: column;
  height: 75vh;
}

.chat-card-header {
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.avatar-large {
  width: 40px;
  height: 40px;
  border-radius: 999px;
  background: linear-gradient(135deg, #6366f1, #3b82f6);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #f9fafb;
  font-weight: 700;
  position: relative;
}

.status-dot {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: #22c55e;
  border: 2px solid #f9fafb;
  bottom: 0;
  right: 0;
}

.chat-title {
  font-weight: 700;
  font-size: 1rem;
}

.chat-subtitle {
  font-size: 0.78rem;
}

.chat-body {
  flex: 1;
  background: radial-gradient(circle at top left, #eef2ff 0, #f9fafb 40%, #ffffff 100%);
  padding: 1rem;
  overflow-y: auto;
}

/* Messages */
.message-row {
  animation: fadeInUp 0.15s ease-in-out;
}

.message-bubble {
  padding: 0.55rem 0.75rem;
  border-radius: 0.9rem;
  max-width: 65%;
  position: relative;
}

.message-in {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  color: #111827;
}

.message-out {
  background: linear-gradient(135deg, #6366f1, #3b82f6);
  color: #f9fafb;
  border: none;
}

.message-text {
  font-size: 0.88rem;
  line-height: 1.35;
}

.message-meta {
  font-size: 0.75rem;
}

.message-time {
  font-size: 0.72rem;
  opacity: 0.85;
}

.read-tick {
  font-size: 0.65rem;
}

/* Bubble tails */
.bubble-tail-left,
.bubble-tail-right {
  content: "";
  position: absolute;
  bottom: 0;
  width: 10px;
  height: 10px;
}

.bubble-tail-left {
  left: -5px;
  border-radius: 0 0 0 1rem;
  box-shadow: -2px 2px 0 0 #ffffff;
}

.message-in .bubble-tail-left {
  box-shadow: -2px 2px 0 0 #ffffff;
}

.bubble-tail-right {
  right: -5px;
  border-radius: 0 0 1rem 0;
  box-shadow: 2px 2px 0 0 #6366f1;
}

/* Avatar small */
.avatar-xs {
  width: 24px;
  height: 24px;
  border-radius: 999px;
  background: #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  color: #4b5563;
}

/* Footer / composer */
.chat-footer {
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}

.btn-icon {
  border-radius: 999px;
  border: none;
  background: #e5e7eb;
  width: 34px;
  height: 34px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #4b5563;
  padding: 0;
}

.btn-icon:hover {
  background: #d1d5db;
}

.chat-input {
  border-radius: 999px;
  border: 1px solid #d1d5db;
  padding-left: 0.9rem;
  padding-right: 0.9rem;
  font-size: 0.9rem;
}

.chat-input:focus {
  box-shadow: none;
  border-color: #6366f1;
}

.chat-send-btn {
  border-radius: 999px;
  padding: 0.4rem 1rem;
  font-size: 0.85rem;
}

.attachment-link {
  font-size: 0.8rem;
  text-decoration: underline;
}

/* Scrollbar */
.chat-body::-webkit-scrollbar,
.reps-list::-webkit-scrollbar {
  width: 6px;
}
.chat-body::-webkit-scrollbar-thumb,
.reps-list::-webkit-scrollbar-thumb {
  background-color: rgba(148, 163, 184, 0.7);
  border-radius: 999px;
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translate3d(0, 4px, 0);
  }
  to {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
}

@media (max-width: 767.98px) {
  .chat-card {
    height: 70vh;
  }
  .message-bubble {
    max-width: 80%;
  }
}
</style>
