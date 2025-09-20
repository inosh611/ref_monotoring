<script setup>
import AdminLayout from '@/Layouts/Admin/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, nextTick, watch, onUnmounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  reps: Array,              // [{id, name}]
  currentPartnerId: Number, // initial selected rep
})

const partnerId = ref(props.currentPartnerId || null)
const partnerName = ref('')
const loading = ref(false)
const sending = ref(false)
const messages = ref([])
const newMessage = ref('')
const file = ref(null)
const poller = ref(null)

const listEl = ref(null)

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
    messages.value = data.messages || []
    scrollToBottom()
    // mark read
    await axios.post(route('chat.read'), { user_id: partnerId.value })
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
  partnerId.value = id
  partnerName.value = name
  loadMessages()
}

onMounted(() => {
  const first = props.reps?.[0]
  if (partnerId.value && !partnerName.value) {
    const found = props.reps.find(r => r.id === partnerId.value)
    partnerName.value = found?.name || ''
  } else if (first && !partnerId.value) {
    partnerId.value = first.id
    partnerName.value = first.name
  }
  loadMessages()
  // simple polling (replace with Echo/Pusher later)
  poller.value = setInterval(loadMessages, 3000)
})

watch(partnerId, () => {
  // whenever partner changes, refresh name
  const found = props.reps.find(r => r.id === partnerId.value)
  partnerName.value = found?.name || ''
})

onUnmounted?.(() => {
  if (poller.value) clearInterval(poller.value)
})
</script>

<template>
  <AdminLayout>
    <Head title="Chat" />
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h4 class="m-0">CHAT</h4></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a :href="route('dashboard')">Dashboard</a></li>
              <li class="breadcrumb-item active">Chat Management</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- Left: reps list -->
          <div class="col-md-3">
            <div class="card">
              <div class="card-header"><strong>Representatives</strong></div>
              <div class="list-group list-group-flush" style="max-height: 70vh; overflow-y:auto;">
                <button v-for="rep in props.reps" :key="rep.id"
                        class="list-group-item list-group-item-action"
                        :class="{'active': rep.id === partnerId}"
                        @click="selectPartner(rep.id, rep.name)">
                  {{ rep.name }}
                </button>
              </div>
            </div>
          </div>

          <!-- Right: conversation -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ partnerName || 'Select a representative' }}</strong>
                <span v-if="loading" class="badge badge-info">Loading…</span>
              </div>

              <div class="card-body" ref="listEl" style="height:60vh; overflow-y:auto; background:#f6f7fb;">
                <div v-if="!partnerId" class="text-center text-muted">Choose a rep to start chatting</div>

                <div v-for="m in messages" :key="m.id"
                     class="d-flex mb-2"
                     :class="m.mine ? 'justify-content-end' : 'justify-content-start'">
                  <div :class="['p-2 rounded', m.mine ? 'bg-primary text-white' : 'bg-white border']"
                       style="max-width: 65%;">
                    <div v-if="!m.mine" class="text-muted small mb-1">{{ m.sender }}</div>
                    <div v-if="m.body">{{ m.body }}</div>
                    <div v-if="m.attachment_url" class="mt-1">
                      <a :href="m.attachment_url" target="_blank" class="text-underline text-white">
                        Attachment
                      </a>
                    </div>
                    <div class="text-right text-muted small mt-1">{{ m.time }}</div>
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <form @submit.prevent="send" class="d-flex gap-2">
                  <input v-model="newMessage" type="text" class="form-control mr-2" placeholder="Type a message…" />
                  <!-- <input ref="file" type="file" class="form-control-file" style="max-width:220px" /> -->
                  <button class="btn btn-primary" :disabled="sending || !partnerId">Send</button>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  </AdminLayout>
</template>
